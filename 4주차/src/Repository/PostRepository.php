<?php
declare(strict_types=1);

namespace Donut\Repository;

use Donut\Entity\Post;

class PostRepository
{
    public function create(array $data): Post
    {
        return Post::create($data);
    }
    
    public function read(int $id): Post
    {
        return Post::find($id);
    }

    public function update(Post $post, array $data): bool
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
