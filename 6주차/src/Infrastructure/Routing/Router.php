<?php
declare(strict_types=1);

namespace Fondue\Infrastructure\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Fondue\Exception\Http\InternalServerErrorException;
use Fondue\Exception\Http\NotFoundException;
use Fondue\Exception\Http\MethodNotAllowedException;
use Psr\Container\ContainerInterface;

class Router
{
    private ?Dispatcher $dispatcher = null;
    
    public function __construct(private ContainerInterface $container)
    {
        $this->container = $container;
    }
    
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
            Dispatcher::FOUND => $this->handleFoundRoute($routeInfo[1], $routeInfo[2]),
        };
    }
    
    /**
     * 매칭된 라우트의 핸들러를 실행합니다.
     * 
     * @param mixed $handler 컨트롤러 배열 또는 클로저
     * @param array $vars 라우트 파라미터
     * @return mixed 핸들러의 반환값
     * @throws InternalServerErrorException 잘못된 핸들러 타입일 때
     */
    private function handleFoundRoute(mixed $handler, array $vars): mixed
    {
        if (is_array($handler)) {
            return $this->executeControllerAction($handler, $vars);
        }
        
        if (is_callable($handler)) {
            return $this->executeCallable($handler, $vars);
        }
        
        throw new InternalServerErrorException("Invalid route handler.");
    }
    
    /**
     * 컨트롤러 액션을 실행합니다.
     * 
     * @param array $handler [컨트롤러 클래스명, 액션 메서드명]
     * @param array $vars 라우트 파라미터
     * @return mixed 컨트롤러 액션의 반환값
     * @throws InternalServerErrorException 컨트롤러나 액션이 존재하지 않을 때
     */
    private function executeControllerAction(array $handler, array $vars): mixed
    {
        [$controllerClass, $action] = $handler;
        
        $controller = $this->container->get($controllerClass);

        if (!method_exists($controller, $action)) {
            throw new InternalServerErrorException(
                "Controller action '{$controllerClass}::{$action}' not found."
            );
        }

        return $controller->{$action}($vars);
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