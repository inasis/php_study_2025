<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Post;

use Hazelnut\Application\UseCase\Post\ViewPostUseCase;
use Hazelnut\Application\DTO\Post\PostResultDTO;
use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Domain\Service\PostDomainService;
use Hazelnut\Domain\Service\UserDomainService;

/**
 * 게시물 조회 유스케이스 구현체
 */
final class ViewPostService implements ViewPostUseCase
{
    public function __construct(
        private readonly PostDomainService $postDomainService,
        private readonly UserDomainService $userDomainService
    ) {}

    public function viewPost(int $postId): PostResultDTO
    {
        // Domain Service를 호출하여 게시물을 조회합니다.
        $postAggregate = $this->postDomainService->findPostById($postId);
        $userAggregate = $this->userDomainService->findUserById($postAggregate->getAuthorId());

        if ($userAggregate === null) {
            // 이 상황은 데이터 불일치를 의미할 수 있으므로, 적절한 예외 처리 또는 기본값 설정이 필요합니다.
            $userResultDTO = new UserResultDTO($postAggregate->getAuthorId(), 'John Doe', 'johndoe@unavailable.com');
        } else {
            $userResultDTO = UserResultDTO::fromAggregate($userAggregate);
        }
        
        // Aggregate와 User DTO를 Application DTO로 변환하여 반환
        return PostResultDTO::fromAggregate($postAggregate, $userResultDTO);
    }
}