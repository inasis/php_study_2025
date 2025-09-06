<?php
declare(strict_types=1);

namespace Proj;

require_once __DIR__ . '/bootstrap.php';
use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title', 'content'];
    public $timestamps = true;
}

class EloquentDatabase
{
    public function createFirstTable(): void
    {
        $capsule = \Illuminate\Database\Capsule\Manager::schema();
        if (!$capsule->hasTable('posts')) {
            $capsule->create('posts', function ($table) {
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

$db = new EloquentDatabase();
$db->createFirstTable();

// Create
$title = "새로운 타이틀";
$content = "새로운 컨텐츠";
$post = $db->create($title, $content);

// Read
$post = $db->read(1);
print_r($post);

// Update
$data['title']="변경된 타이틀";
$db->update(1, $data);

// Delete
$db->delete(4);
