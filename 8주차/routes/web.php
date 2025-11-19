<?php
declare(strict_types=1);

use FastRoute\RouteCollector;
use Hazelnut\Presentation\Web\Controller\AuthenticationController;
use Hazelnut\Presentation\Web\Controller\PostController;
use Hazelnut\Presentation\Web\Controller\UserController;
use Hazelnut\Application\Middleware\AuthMiddleware;

/*
 라우트 배열 형식:
 [
   'handler' => $handler,
   'before'  => [ MiddlewareClassA::class, MiddlewareClassB::class ],
   'after'   => [ MiddlewareClassX::class ]
 ]
*/

return function(RouteCollector $r) {
    // 단순 public route
    $r->addRoute('GET', '/', [
        'handler' => fn() => ['title' => "Hello world", 'content' => 'Lorem ipsum dolor sit amet'],
    ]);

    $r->addRoute('POST', '/login', [
        'handler' => [AuthenticationController::class, 'login'],
    ]);

    $r->addRoute('GET', '/logout', [
        'handler' => [AuthenticationController::class, 'logout'],
    ]);

    $r->addRoute('POST', '/user', [
        'handler' => [UserController::class, 'register'],
    ]);

    $r->addRoute('GET', '/user', [
        'handler' => [UserController::class, 'read'],
        'before'  => [AuthMiddleware::class],
    ]);

    $r->addRoute('PUT', '/user', [
        'handler' => [UserController::class, 'update'],
        'before'  => [AuthMiddleware::class],
    ]);

    $r->addRoute('DELETE', '/user', [
        'handler' => [UserController::class, 'delete'],
        'before'  => [AuthMiddleware::class],
    ]);

    $r->addRoute('POST', '/post', [
        'handler' => [PostController::class, 'create'],
        'before'  => [AuthMiddleware::class],
    ]);

    $r->addRoute('GET', '/post/{id:\d+}', [
        'handler' => [PostController::class, 'viewPost'],
        'before'  => [],
        'after'   => [],
        'viewer'  => ['post.html'],
    ]);

    $r->addRoute('PUT', '/post', [
        'handler' => [PostController::class, 'update'],
        'before'  => [AuthMiddleware::class],
    ]);

    $r->addRoute('DELETE', '/post/{id:\d+}', [
        'handler' => [PostController::class, 'delete'],
        'before'  => [AuthMiddleware::class],
    ]);
};