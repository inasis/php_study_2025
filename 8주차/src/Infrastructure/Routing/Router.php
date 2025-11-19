<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;
use Hazelnut\Infrastructure\Routing\Exception\NotFoundException;
use Hazelnut\Infrastructure\Routing\Exception\MethodNotAllowedException;
use Hazelnut\Infrastructure\Routing\Exception\InternalServerErrorException;
use function FastRoute\simpleDispatcher;
use function array_key_exists;
use function basename;
use function str_replace;

class Router
{
    private ?Dispatcher $dispatcher = null;
    
    public function __construct(
        private ContainerInterface $container
    ) {}
    
    /**
     * FastRoute 디스패처를 실행하여 매칭된 라우트를 처리하고 최종 응답 값(배열, DTO 객체 또는 문자열)을 반환합니다.
     *
     * @param string $httpMethod HTTP 요청 메서드
     * @param string $uri 요청 URI
     * @return mixed 컨트롤러가 반환한 값 (예: array, DTO 객체, string)
     * @throws NotFoundException 라우트 매칭 실패 시
     * @throws MethodNotAllowedException HTTP 메서드가 허용되지 않을 시
     * @throws InternalServerErrorException 컨트롤러/액션 오류 시
     */
    public function dispatch(string $httpMethod, string $uri): mixed
    {
        $uri = $this->sanitizeUri($uri);

        /**
         * @var array{
         * 0: Dispatcher::NOT_FOUND|Dispatcher::METHOD_NOT_ALLOWED|Dispatcher::FOUND,
         * 1: mixed, // 콜러블 및 미들웨어 정의 배열: ['handler' => callable, 'middleware' => array, 'after' => array]
         * 2: array<string,string>
         * }
         */
        $routeInfo = $this->getDispatcher()->dispatch($httpMethod, $uri);
        
        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => throw new NotFoundException(),
            Dispatcher::METHOD_NOT_ALLOWED => throw new MethodNotAllowedException("Method Not Allowed, Allow: " . implode(', ', $routeInfo[1])),
            Dispatcher::FOUND => $this->handleFoundRoute($routeInfo[1], $routeInfo[2], $httpMethod),
        };
    }
    
    /**
     * 매칭된 라우트의 핸들러(연관 배열)를 실행합니다.
     *
     * @param array $handler 라우트 정의 배열
     * @param array $params 라우트 파라미터 (URL 파라미터)
     * @param string $httpMethod HTTP 요청 메서드
     * @return mixed 컨트롤러가 반환한 값
     */
    private function handleFoundRoute(array $handler, array $params, string $httpMethod): mixed // <<-- (2) string -> mixed 변경
    {
        // 필수 키 검증
        if (!array_key_exists('handler', $handler)) {
            throw new InternalServerErrorException("Route definition must contain a 'handler' key.");
        }

        // 기본값 설정
        $callable = $handler['handler'];
        $preMiddlewareClasses = $handler['before'] ?? [];
        $viewer = $handler['viewer'] ?? [];

        // 요청 데이터 준비
        $requestData = $this->getRequestData($httpMethod);
        $routeParams = array_merge($params, ['requestData' => $requestData]);

        // Pre-Middleware 실행 (요청 처리 전)
        $middlewareResult = $this->runPreMiddlewareChain($preMiddlewareClasses);

        if (is_array($middlewareResult) && array_key_exists('error', $middlewareResult)) {
            // 미들웨어에서 에러 응답이 반환된 경우, 즉시 차단
            // 이 경우 응답은 배열입니다.
            return $middlewareResult;
        }

        // 최종 파라미터에 미들웨어 결과 통합
        $routeParams = array_merge(
            $routeParams, 
            $middlewareResult ? ['middleware' => $middlewareResult] : [],
        );
        
        // 콜러블 실행
        $responseContent = $this->executeCallable($callable, $routeParams);
        return array_merge($responseContent, ['view' => $handler['viewer']]);
    }
    
    /**
     * 최종 콜러블(컨트롤러 메서드 또는 클로저)을 실행합니다.
     * * @return mixed 컨트롤러가 반환한 값 (DTO 객체, 배열, 또는 문자열)
     */
    private function executeCallable(mixed $callable, array $params): mixed // <<-- (3) string -> mixed 변경
    {
        if (is_array($callable) && count($callable) === 2 && is_string($callable[0]) && is_string($callable[1])) {
            // 컨트롤러/메서드 형태: [ControllerClass::class, 'methodName']
            $controllerClass = $callable[0];
            $methodName = $callable[1];

            if (!$this->container->has($controllerClass)) {
                throw new InternalServerErrorException("Controller '{$controllerClass}' not found in container.");
            }

            try {
                $controllerInstance = $this->container->get($controllerClass);
            } catch (\Throwable $e) {
                throw new InternalServerErrorException("Failed to instantiate controller '{$controllerClass}': " . $e->getMessage(), 0, $e);
            }

            if (!method_exists($controllerInstance, $methodName)) {
                throw new InternalServerErrorException("Method '{$methodName}' not found on controller '{$controllerClass}'.");
            }
            
            // 컨트롤러 메서드 실행
            $result = $controllerInstance->{$methodName}($params);

            // DTO 객체이거나 배열인 경우, toArray를 통해 배열로 변환
            if (is_object($result) && method_exists($result, 'toArray')) {
                // toArray() 메서드가 있는 DTO 객체를 배열로 변환
                $result = $result->toArray(); // <<-- (4) JSON 인코딩 제거
            }
            
            // 만약 배열이 아니거나, 문자열이 아닌 다른 객체라면 그대로 반환
            return $result; 

        } elseif (is_callable($callable)) {
            // 클로저 형태 (파라미터는 배열 전체를 전달)
            return $callable($params); // <<-- (4) (string) 강제 형 변환 제거
            
        } else {
            throw new InternalServerErrorException("Invalid callable format provided in route handler.");
        }
    }

    /**
     * Pre-Middleware 체인을 실행합니다. (Router::dispatch 이전에 실행)
     * * @param array<int, class-string> $middlewareClasses
     * @return array<string, mixed>|null 미들웨어 결과 (성공 시), 또는 미들웨어 응답 배열 (차단 시)
     * @throws InternalServerErrorException
     */
    private function runPreMiddlewareChain(array $middlewareClasses): array|null
    {
        $results = [];

        foreach ($middlewareClasses as $middlewareClass) {
            if (!is_string($middlewareClass) || !$this->container->has($middlewareClass)) {
                throw new InternalServerErrorException(
                    "Pre-Middleware '{$middlewareClass}' not found or invalid format in container."
                );
            }

            $middleware = $this->container->get($middlewareClass);

            // Pre-Middleware는 handle() 메서드를 인자 없이 호출하고, 
            // 결과를 반환하거나 에러 배열을 반환해 요청을 차단합니다.
            if (!method_exists($middleware, 'handle')) {
                throw new InternalServerErrorException(
                    "Pre-Middleware '{$middlewareClass}' must implement a handle() method."
                );
            }

            $result = $middleware->handle();

            if (is_array($result) && array_key_exists('error', $result)) {
                return $result; // 에러 응답 즉시 반환 (요청 차단)
            }

            if ($result !== null) {
                $key = basename(str_replace('\\', '/', $middlewareClass)); 
                $results[$key] = $result;
            }
        }

        return !empty($results) ? $results : null;
    }

    private function getDispatcher(): Dispatcher
    {
        if ($this->dispatcher === null) {
            $this->dispatcher = $this->createDispatcher();
        }
        
        return $this->dispatcher;
    }

    private function getRequestData(string $httpMethod): array
    {
        if (in_array($httpMethod, ['POST', 'PUT', 'PATCH'])) {
            $input = file_get_contents('php://input');
            return json_decode($input, true) ?? [];
        }
        // GET/DELETE 요청에 대한 쿼리 파라미터는 $params에 이미 포함되어 있으므로, 
        // 여기서 requestData는 POST/PUT/PATCH 요청 본문만 처리합니다.
        return [];
    }

    private function createDispatcher(): Dispatcher
    {
        // 이제 핸들러는 연관 배열입니다.
        return simpleDispatcher(function(RouteCollector $r) {
            $routes = require __DIR__ . '/../../../routes/web.php';
            $routes($r);
        });
    }
    
    private function sanitizeUri(string $uri): string
    {
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        
        return rawurldecode($uri);
    }
}