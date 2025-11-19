<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Post;

use Hazelnut\Application\DTO\Post\PublishPostCommand;
use Hazelnut\Application\DTO\Post\PostResultDTO;

/**
 * 게시물 생성 유스케이스
 */
interface PublishPostUseCase
{
    public function publishPost(PublishPostCommand $publishPostCommand): PostResultDTO;
}