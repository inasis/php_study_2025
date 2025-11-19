<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Post;

/**
 * 게시물 배치 조회 유스케이스
 */
interface BatchViewPostUseCase
{
    public function batchViewPost(array $postIds): array;
}