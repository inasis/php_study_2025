<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Post;

use Hazelnut\Application\UseCase\Post\RemovePostUseCase;
use Hazelnut\Domain\Service\PostDomainService;

/**
 * 게시물 삭제 유스케이스 구현체
 */
final class RemovePostService implements RemovePostUseCase
{
    public function __construct(
        private readonly PostDomainService $postDomainService
    ) {}

    public function removePost(int $postId): void
    {
        // Domain Service를 호출하여 게시물을 삭제합니다.
        $this->postDomainService->deletePost($postId);

        // 삭제는 반환 값이 없습니다.
    }
}