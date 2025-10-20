<?php
declare(strict_types=1);

require(__DIR__ . "/../vendor/autoload.php");

use Fondue\Bootstrap; 
use Fondue\Infrastructure\Routing\Router;
use Fondue\Exception\ExceptionHandler;

$container = Bootstrap::initialize(); 

// 예외 핸들러 등록
$exceptionHandler = $container->get(ExceptionHandler::class);
$exceptionHandler->register();

// 라우터 디스패치
$result = $container->get(Router::class)->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

// POST 없이 임의로 글을 생성합니다.
// $controller = $container->get(PostController::class)->create(['title' => 'Post title', 'content' => 'lorem ipsum dolor sit amet']);

// 굉장히 작고 소중한 View를 index에 직접 만들었습니다.
header('Content-Type: application/json; charset=utf-8');
header('HTTP/1.1 200 OK');

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

exit;