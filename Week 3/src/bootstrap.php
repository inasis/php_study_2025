<?php
declare(strict_types=1);
namespace Citrus;

use Citrus\Database;

require __DIR__ . '/../vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__ . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'sqlite',
    'database'  => __DIR__ . '/../database.sqlite',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$database = new Database();
$database->createFirstTable();

// Create
$title = "새로운 타이틀";
$content = "새로운 컨텐츠";
$post = $database->create($title, $content);

// Read
$post = $database->read(1);
print_r($post);

// Update
$data['title']="변경된 타이틀";
$database->update(1, $data);

// Delete
$database->delete(2);