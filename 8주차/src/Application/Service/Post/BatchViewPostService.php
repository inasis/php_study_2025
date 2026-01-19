<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service;

use Hazelnut\Application\UseCase\Post\BatchViewPostUseCase;
use Hazelnut\Application\DTO\Post\PostResultDTO;
use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Domain\Service\PostDomainService;
use Hazelnut\Domain\Service\UserDomainService;

/**
 * 게시물 배치 조회 유스케이스 구현체
 */
class BatchViewPostService implements BatchViewPostUseCase
{
    public function __construct(
        private readonly PostDomainService $postDomainService,
        private readonly UserDomainService $userDomainService
    ) {}

    public function batchViewPost(array $postIds): array
    {
        if (empty($postIds)) {
            return [];
        }

        // Domain Service를 호출하여 게시물 목록을 조회합니다.
        $postAggregates = $this->postDomainService->findPostByIds($postIds);

        if (empty($postAggregates)) {
            return [];
        }

        $authorIds = [];
        foreach ($postAggregates as $postValue) {
            $authorIds[] = $postValue->getAuthorId();
        }
        $authorIds = array_unique($authorIds);

        // User 도메인에서 ID 기반으로 UserAggregate 조회 후 맵 생성
        $userAggregates = $this->userDomainService->findUserByIds($authorIds);
        $userMap = [];
        foreach ($userAggregates as $userValue) {
            $userMap[$userValue->getId()] = $userValue;
        }

        $postResultDTOs = [];
        foreach ($postAggregates as $postValue) {
            $userAggregate = $userMap[$postValue->getAuthorId()] ?? null;

            if ($userAggregate === null) {
                // 이 상황은 데이터 불일치를 의미할 수 있으므로, 적절한 예외 처리 또는 기본값 설정이 필요합니다.
                $userResultDTO = new UserResultDTO($postValue->getAuthorId(), 'John Doe', 'johndoe@unavailable.com');
            } else {
                $userResultDTO = UserResultDTO::fromAggregate($userAggregate);
            }
            
            // Aggregate와 User DTO를 Application DTO로 변환하여 배열에 저장
            $postResultDTOs[] = PostResultDTO::fromAggregate($postValue, $userResultDTO);
        }

        return $postResultDTOs;
    }
}