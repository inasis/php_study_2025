<?php
declare(strict_types=1);

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