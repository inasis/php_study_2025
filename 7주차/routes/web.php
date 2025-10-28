<?php
declare(strict_types=1);

use FastRoute\RouteCollector;
use Ginger\Controller\AuthenticationController;
use Ginger\Controller\PostController;
use Ginger\Controller\UserController;
use Ginger\Controller\Middleware\AuthMiddleware;

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
        'before'  => [],
        'after'   => [],
    ]);

    $r->addRoute('POST', '/login', [
        'handler' => [AuthenticationController::class, 'login'],
        'before'  => [],
        'after'   => [],
    ]);

    $r->addRoute('GET', '/logout', [
        'handler' => [AuthenticationController::class, 'logout'],
        'before'  => [],
        'after'   => [],
    ]);

    $r->addRoute('POST', '/user', [
        'handler' => [UserController::class, 'register'],
        'before'  => [],
        'after'   => [],
    ]);

    $r->addRoute('GET', '/user', [
        'handler' => [UserController::class, 'read'],
        'before'  => [AuthMiddleware::class],
        'after'   => [],
    ]);

    $r->addRoute('PUT', '/user', [
        'handler' => [UserController::class, 'update'],
        'before'  => [AuthMiddleware::class],
        'after'   => [],
    ]);

    $r->addRoute('DELETE', '/user', [
        'handler' => [UserController::class, 'delete'],
        'before'  => [AuthMiddleware::class],
        'after'   => [],
    ]);

    $r->addRoute('POST', '/post', [
        'handler' => [PostController::class, 'create'],
        'before'  => [AuthMiddleware::class],
        'after'   => [],
    ]);

    $r->addRoute('GET', '/post/{id:\d+}', [
        'handler' => [PostController::class, 'read'],
        'before'  => [],
        'after'   => [],
    ]);

    $r->addRoute('PUT', '/post', [
        'handler' => [PostController::class, 'update'],
        'before'  => [AuthMiddleware::class],
        'after'   => [],
    ]);

    $r->addRoute('DELETE', '/post/{id:\d+}', [
        'handler' => [PostController::class, 'delete'],
        'before'  => [AuthMiddleware::class],
        'after'   => [],
    ]);
};