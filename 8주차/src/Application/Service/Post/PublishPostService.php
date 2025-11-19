<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Post;

use Hazelnut\Application\UseCase\Post\PublishPostUseCase;
use Hazelnut\Application\DTO\Post\PublishPostCommand;
use Hazelnut\Application\DTO\Post\PostResultDTO;
use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Domain\Service\PostDomainService;
use Hazelnut\Domain\Service\UserDomainService;

/**
 * 게시물 생성 유스케이스 구현체
 */
final class PublishPostService implements PublishPostUseCase
{
    public function __construct(
        private readonly PostDomainService $postDomainService,
        private readonly UserDomainService $userDomainService
    ) {}

    public function publishPost(PublishPostCommand $publishPostCommand): PostResultDTO
    {
        // DTO에서 원시 데이터 추출
        $title = $publishPostCommand->title;
        $content = $publishPostCommand->content;
        $authorId = $publishPostCommand->authorId;

        // Domain Service를 호출하여 게시물을 생성합니다.
        // PostResultDTO에 포함될 생성된 게시물의 작성자 정보를 가져옵니다.
        $postAggregate = $this->postDomainService->createPost($title, $content, $authorId);
        $userAggregate = $this->userDomainService->findUserById($authorId);
        $userResultDTO = UserResultDTO::fromAggregate($userAggregate);

        // Aggregate와 User DTO를 Application DTO로 변환하여 반환
        return PostResultDTO::fromAggregate($postAggregate, $userResultDTO);
    }
}