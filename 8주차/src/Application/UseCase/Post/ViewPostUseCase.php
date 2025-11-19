<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Post;

use Hazelnut\Application\DTO\Post\PostResultDTO;

/**
 * 게시물 조회 유스케이스
 */
interface ViewPostUseCase
{
    public function viewPost(int $postId): PostResultDTO;
}