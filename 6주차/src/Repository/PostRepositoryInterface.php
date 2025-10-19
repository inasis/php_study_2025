<?php
declare(strict_types=1);

namespace Fondue\Repository;

use Fondue\Entity\Post;

interface PostRepositoryInterface
{
    public function create(array $data): Post;
    public function read(array $data): ?Post;
    public function update(Post $post, array $data): Post;
    public function delete(Post $post): void;
}