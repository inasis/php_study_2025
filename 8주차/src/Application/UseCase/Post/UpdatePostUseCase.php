<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Post;

use Hazelnut\Application\DTO\Post\UpdatePostCommand;
use Hazelnut\Application\DTO\Post\PostResultDTO;

/**
 * 게시물 수정 유스케이스
 */
interface UpdatePostUseCase
{
    public function updatePost(UpdatePostCommand $updatePostCommand): PostResultDTO;
}