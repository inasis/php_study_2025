<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Post;

/**
 * 게시물 삭제 유스케이스
 */
interface RemovePostUseCase
{
    public function removePost(int $postId): void;
}