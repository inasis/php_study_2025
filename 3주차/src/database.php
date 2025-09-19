<?php
declare(strict_types=1);

namespace Citrus;

use Illuminate\Database\Capsule\Manager as Capsule;
use Citrus\Entity\Post;

class Database
{
    public function __construct()
    {
        $this->initDatabaseFile();
        $this->initConnection();
        $this->createFirstTable();
    }

    private function initDatabaseFile(): void
    {
        $dbPath = __DIR__ . '/../database.sqlite';
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }
    }

    private function initConnection(): void
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'sqlite',
            'database'  => __DIR__ . '/../database.sqlite',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function createFirstTable(): void
    {
        $schema = Capsule::schema();
        if (!$schema->hasTable('posts')) {
            $schema->create('posts', function ($table) {
                $table->increments('id');
                $table->string('title');
                $table->text('content');
                $table->timestamps();
            });
        }
    }

    public function create(string $title, string $content): Post
    {
        return Post::create(['title' => $title, 'content' => $content]);
    }

    public function read(int $id): ?Post
    {
        return Post::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $post = Post::find($id);
        if (!$post) return false;
        $post->fill($data);
        return $post->save();
    }

    public function delete(int $id): bool
    {
        $post = Post::find($id);
        if (!$post) return false;
        return (bool)$post->delete();
    }
}
