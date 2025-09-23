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

return $container;