<?php
declare(strict_types=1);

$container = require(__DIR__ . "/../src/bootstrap.php");

use Donut\Controller\PostController;

$controller = $container->get(PostController::class);

// POST 없이 임의로 글을 생성합니다.
$controller->create(['title' => 'test', 'content' => 'lorem ipsum dolor sit amet']);

// POST /posts - 생성
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/posts') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'content' => trim($_POST['content'] ?? '')
    ];
    $controller->create($data);
}

// GET /posts?id=123 - 조회
if ($_SERVER['REQUEST_METHOD'] === 'GET' && str_starts_with($_SERVER['REQUEST_URI'], '/posts')) {
    $params = [
        'id' => filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT)
    ];
    $controller->read($params);
}

// PUT /posts - 수정
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // PUT 데이터 파싱 (JSON이나 form-data)
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $data = [
        'id' => (int)($input['id'] ?? 0),
        'title' => $input['title'] ?? null,
        'content' => $input['content'] ?? null
    ];
    $controller->update($data);
}
