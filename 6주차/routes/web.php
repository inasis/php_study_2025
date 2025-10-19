<?php
declare(strict_types=1);

use Fondue\Controller\PostController;

return function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', function() {
        return ['title'=>"Hello world", 'content'=>'Lorem ipsum dolor sit amet'];
    });

    $r->addRoute('POST', '/post', [PostController::class, 'create']);
    $r->addRoute('GET', '/post/{id:\d+}', [PostController::class, 'read']);
    $r->addRoute('PUT', '/post', [PostController::class, 'update']);
    $r->addRoute('DELETE', '/post/{id:\d+}', [PostController::class, 'delete']);
};