<?php
declare(strict_types=1);

$container = require(__DIR__ . "/../src/bootstrap.php");

use Egg\Controller\PostController;
use Egg\Exception\NotFoundException;
use Egg\Exception\InternalServerErrorException;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('POST', '/post', 'createPost');
    $r->addRoute('GET', '/post/{id:\d+}', 'readPost');
    $r->addRoute('PUT', '/post', 'updatePost');
    $r->addRoute('DELETE', '/post/{id:\d+}', 'deletePost');
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// FastRoute는 쿼리 스트링을 사용하지 않습니다
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // 404 Not Found
        header("HTTP/1.0 404 Not Found");
        throw new NotFoundException();
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // 405 Method Not Allowed
        header("HTTP/1.0 405 Method Not Allowed");
        header("Allow: " . implode(', ', $allowedMethods));
        echo "405 Method Not Allowed";
        break;
    
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $controller = $container->get(PostController::class);

        // POST 없이 임의로 글을 생성합니다.
        //$controller->create(['title' => 'yes', 'content' => 'lorem ipsum dolor sit amet']);

        switch($handler) {
            case 'createPost':
                $data = [
                    'title' => trim($_POST['title'] ?? ''),
                    'content' => trim($_POST['content'] ?? '')
                ];
                $controller->create($data);
                break;

            case 'readPost':
                $params = [
                    'id' => (int)($vars['id'] ?? 0)
                ];
                $controller->read($params);
                break;

            case 'updatePost':
                $input = json_decode(file_get_contents('php://input'), true) ?? [];
                $data = [
                    'id' => (int)($input['id'] ?? 0),
                    'title' => $input['title'] ?? null,
                    'content' => $input['content'] ?? null
                ];
                $controller->update($data);
                break;

            case 'deletePost':
                $params = [
                    'id' => (int)($vars['id'] ?? 0)
                ];
                $controller->delete($params);
                break;

            default:
                throw new InternalServerErrorException();
        }
        break;
}

