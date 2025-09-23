<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;

// DI 컨테이너 초기화
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);

// 서비스와 컨트롤러를 컨테이너에 등록합니다
$containerBuilder->addDefinitions([
    \Donut\Service\PostService::class => \DI\autowire(),
    \Donut\Validation\Validator::class => \DI\autowire(),
    \Donut\Controller\PostController::class => \DI\autowire(),
]);

$container = $containerBuilder->build();

// 데이터베이스 초기화
$dbPath = __DIR__ . '/../database.sqlite';
// 파일이 없으면 생성
if (!file_exists($dbPath)) {
    touch($dbPath);
    chmod($dbPath, 0666);
}

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'sqlite',
    'database'  => $dbPath,
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// 테이블이 존재하지 않을 경우 생성합니다
if (!$capsule::schema()->hasTable('posts')) {
    $capsule::schema()->create('posts', function ($table) {
        $table->increments('id');
        $table->string('title');
        $table->text('content');
        $table->timestamps();
    });
}


use Donut\Controller\PostController;

$controller = $container->get(PostController::class);

// POST 없이 임의로 글을 생성합니다.
$controller->create("새로운 글", "테스트를 위한 컨텐츠");

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