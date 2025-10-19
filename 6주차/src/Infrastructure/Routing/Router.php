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
    private ContainerInterface $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * FastRoute 디스패처를 실행하여 매칭 결과에 따라 컨트롤러를 실행하고
     * 결과를 반환하거나 예외를 던집니다.
     * 
     * @param string $httpMethod HTTP 요청 메서드
     * @param string $uri 요청 URI
     * @return mixed 컨트롤러 액션의 반환 값 (응답 데이터)
     * @throws NotFoundException 라우트 매칭 실패 시
     * @throws MethodNotAllowedException HTTP 메서드가 허용되지 않을 시
     * @throws InternalServerErrorException 컨트롤러/액션 오류 시
     */
    public function dispatch(string $httpMethod, string $uri): mixed
    {
        $uri = $this->sanitizeUri($uri);
        
        // 요청 메서드와 URI를 기반으로 라우트 정의에서 일치하는 규칙을 검색합니다.
        $routeInfo = $this->createDispatcher()->dispatch($httpMethod, $uri);
        
        // Match 절을 사용하여 라우트 정보를 직접 처리하고 컨트롤러를 실행합니다.
        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => throw new NotFoundException(),
            Dispatcher::METHOD_NOT_ALLOWED => throw new MethodNotAllowedException(),
            Dispatcher::FOUND => (function () use ($routeInfo) {
                // $routeInfo[1] = [$controllerClass, $action], $routeInfo[2] = $vars
                [$controllerClass, $action] = $routeInfo[1];
                $vars = $routeInfo[2];

                // DI 컨테이너를 사용하여 컨트롤러 인스턴스를 가져옵니다.
                $controller = $this->container->get($controllerClass);

                if (!method_exists($controller, $action)) {
                    // 라우트 설정 오류이므로 500 에러를 던집니다.
                    throw new InternalServerErrorException("Controller action '{$controllerClass}::{$action}' not found.");
                }

                // 컨트롤러 액션을 호출하고 URI 매개변수($vars)를 전달합니다.
                return $controller->{$action}($vars);
            })(),
        };
    }
    
    private function createDispatcher(): Dispatcher
    {
        return \FastRoute\simpleDispatcher(function(RouteCollector $r) {
            $routes = require __DIR__ . '/../../../config/routes.php';
            $routes($r);
        });
    }
    
   /**
     * URI에서 쿼리 스트링을 제거하고 URL 인코딩을 디코딩하여
     * 라우팅 시스템이 처리할 수 있는 깔끔한 경로 문자열로 정제합니다.
     * 
     * @param string $uri 원본 URI
     * @return string 정제된 URI 경로
     */
    private function sanitizeUri(string $uri): string
    {
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        
        return rawurldecode($uri);
    }
}