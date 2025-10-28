<?php
declare(strict_types=1);

namespace Ginger\Infrastructure\Routing;

use Ginger\Entity\User;
use Ginger\Controller\Middleware\AuthMiddleware;
use Ginger\Exception\Http\InternalServerErrorException;
use Ginger\Exception\Http\NotFoundException;
use Ginger\Exception\Http\MethodNotAllowedException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;

class Router
{
    private ?Dispatcher $dispatcher = null;
    
    public function __construct(
        private ContainerInterface $container
    ) {}
    
    /**
     * FastRoute 디스패처를 실행하여 매칭 결과에 따라 컨트롤러를 실행하고
     * 결과를 반환하거나 예외를 던집니다.
     *
     * @param string $httpMethod HTTP 요청 메서드
     * @param string $uri 요청 URI
     * @return mixed 핸들러의 반환값
     * @throws NotFoundException 라우트 매칭 실패 시
     * @throws MethodNotAllowedException HTTP 메서드가 허용되지 않을 시
     * @throws InternalServerErrorException 컨트롤러/액션 오류 시
     */
    public function dispatch(string $httpMethod, string $uri): mixed
    {
        $uri = $this->sanitizeUri($uri);
        $routeInfo = $this->getDispatcher()->dispatch($httpMethod, $uri);
        
        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => throw new NotFoundException(),
            Dispatcher::METHOD_NOT_ALLOWED => throw new MethodNotAllowedException(),
            Dispatcher::FOUND => $this->handleFoundRoute($routeInfo[1], $routeInfo[2], $httpMethod),
        };
    }
    
    /**
     * 매칭된 라우트의 핸들러를 실행합니다.
     *
     * @param mixed $handler 컨트롤러 배열, 클로저 또는 메타데이터 배열
     * @param array $vars 라우트 파라미터
     * @return mixed 핸들러의 반환값
     * @throws InternalServerErrorException 잘못된 핸들러 타입일 때
     */
    private function handleFoundRoute(mixed $handler, array $vars, string $httpMethod): mixed
    {
        $requestData = $this->getRequestData($httpMethod);

        // 메타데이터 배열: ['handler' => [Controller::class, 'action'], 'middleware' => [...]]
        if (is_array($handler) && isset($handler['handler'])) {
            $middlewares = $handler['middleware'] ?? [];
            $actionHandler = $handler['handler'];

            // 미들웨어 체인 실행
            $middlewareResult = $this->runMiddlewareChain($middlewares);

            // 미들웨어가 에러 응답 배열을 반환하면, 즉시 그 응답을 반환하여 컨트롤러 실행을 막습니다.
            if (is_array($middlewareResult)) {
                return $middlewareResult;
            }
            
            // 미들웨어 결과를 파라미터에 추가 (인증된 User 객체)
            // 컨트롤러 액션이 User 객체를 필요로 할 경우, 이 객체를 Vars에 넣어 전달합니다.
            if ($middlewareResult instanceof User) {
                // $vars['auth_user'] 와 같이 추가 파라미터를 사용하거나
                // 컨트롤러 실행 로직을 수정하여 $middlewareResult를 직접 전달해야 합니다.
                // 현재 구조는 $vars만 컨트롤러에 전달하므로, 간단하게 $vars에 추가합니다.
                $vars['auth_user'] = $middlewareResult;
            }

            // 실제 컨트롤러 액션 실행
            return $this->executeControllerAction($actionHandler, $vars, $requestData);
        }
        
        // [컨트롤러, 액션] 형태 (미들웨어 없는 레거시)
        if (is_array($handler)) {
            return $this->executeControllerAction($handler, $vars, $requestData);
        }
        
        // 클로저 핸들러
        if (is_callable($handler)) {
            return $this->executeCallable($handler, $vars);
        }
        
        throw new InternalServerErrorException("Invalid route handler.");
    }

    /**
     * 라우트에 정의된 미들웨어 체인을 실행합니다.
     * 
     * @param array $middlewareClasses 미들웨어 클래스명 배열
     * @return User|array|null 인증된 User 객체, 에러 배열, 또는 null
     */
    private function runMiddlewareChain(array $middlewareClasses): User|array|null
    {
    foreach ($middlewareClasses as $middlewareClass) {
            // 미들웨어 인스턴스를 컨테이너에서 가져옵니다.
            $middleware = $this->container->get($middlewareClass);

            // authenticate() 메서드가 존재하는지 확인하고 호출합니다.
            if (!method_exists($middleware, 'authenticate')) {
                throw new InternalServerErrorException(
                    "Middleware '{$middlewareClass}' must implement the authenticate() method."
                );
            }
            
            // authenticate() 메서드 호출
            $result = $middleware->authenticate();

            // 미들웨어가 에러 배열을 반환하면 즉시 체인을 중단하고 에러를 반환합니다.
            if (is_array($result)) {
                return $result; 
            }
            
            // User 객체가 반환되면 인증 성공
            if ($result instanceof User) {
                // 현재 구조는 인증 미들웨어 하나만 User 객체를 반환한다고 가정합니다.
                return $result;
            }
        }

        return null;
    }
    
    /**
     * 컨트롤러 액션을 실행합니다.
     * 
     * @param array $handler [컨트롤러 클래스명, 액션 메서드명]
     * @param array $vars 라우트 파라미터 (미들웨어 결과가 auth_user로 포함될 수 있음)
     * @return mixed 컨트롤러 액션의 반환값
     * @throws InternalServerErrorException 컨트롤러나 액션이 존재하지 않을 때
     */
    private function executeControllerAction(mixed $handler, array $vars, array $requestData): mixed
    {
        if (is_array($handler)) {
            [$controllerClass, $action] = $handler;
            
            $controller = $this->container->get($controllerClass);

            if (!method_exists($controller, $action)) {
                throw new InternalServerErrorException(
                    "Controller action '{$controllerClass}::{$action}' not found."
                );
            }

            // $vars는 라우트 파라미터와 미들웨어 결과를 모두 포함합니다.
            // 컨트롤러는 이제 인증/인가 로직 없이 $vars['auth_user']를 사용할 수 있습니다.
            return $controller->{$action}($vars, $requestData);
        }

        if (is_callable($handler)) {
            return $handler($vars);
        }

        throw new InternalServerErrorException('Unsupported handler type for route.');
    }
    
    /**
     * 클로저 핸들러를 실행합니다.
     * 
     * @param callable $handler 클로저 또는 callable
     * @param array $vars 라우트 파라미터
     * @return mixed 핸들러의 반환값
     */
    private function executeCallable(callable $handler, array $vars): mixed
    {
        return $handler(...array_values($vars));
    }
    
    /**
     * FastRoute Dispatcher 인스턴스를 가져옵니다.
     * 인스턴스가 없으면 생성합니다.
     * 
     * @return Dispatcher
     */
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
        return [];
    }

    /**
     * FastRoute Dispatcher를 생성하고 라우트를 등록합니다.
     * 
     * @return Dispatcher
     */
    private function createDispatcher(): Dispatcher
    {
        return \FastRoute\simpleDispatcher(function(RouteCollector $r) {
            $routes = require __DIR__ . '/../../../routes/web.php';
            $routes($r);
        });
    }
    
    /**
     * URI에서 쿼리 스트링을 제거하고 URL 인코딩을 디코딩하여
     * 라우팅 시스템이 처리할 수 있는 깔끔한 경로 문자열로 정제합니다.
     * 
     * @param string $uri 원본 URI
     * @return string 정제된 URI
     */
    private function sanitizeUri(string $uri): string
    {
        // 쿼리 스트링 제거
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        
        return rawurldecode($uri);
    }
}