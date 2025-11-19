<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Post;

use Hazelnut\Application\UseCase\Post\UpdatePostUseCase;
use Hazelnut\Application\DTO\Post\UpdatePostCommand;
use Hazelnut\Application\DTO\Post\PostResultDTO;
use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Domain\Service\PostDomainService;
use Hazelnut\Domain\Service\UserDomainService;

/**
 * 게시물 수정 유스케이스 구현체
 */
final class UpdatePostService implements UpdatePostUseCase
{
    public function __construct(
        private readonly PostDomainService $postDomainService,
        private readonly UserDomainService $userDomainService
    ) {}

    public function updatePost(UpdatePostCommand $updatePostCommand): PostResultDTO
    {
        // DTO에서 원시 데이터 추출
        $id = $updatePostCommand->id;
        $title = $updatePostCommand->title;
        $content = $updatePostCommand->content;

        // Domain Service를 호출하여 게시물을 수정합니다.
        // PostResultDTO에 포함될 수정된 게시물의 작성자 정보를 가져옵니다.
        $postAggregate = $this->postDomainService->updatePost($id, $title, $content);
        $userAggregate = $this->userDomainService->findUserById($postAggregate->getAuthorId());
        $userResultDTO = UserResultDTO::fromAggregate($userAggregate);

        // Aggregate와 User DTO를 Application DTO로 변환하여 반환
        return PostResultDTO::fromAggregate($postAggregate, $userResultDTO);
    }
}