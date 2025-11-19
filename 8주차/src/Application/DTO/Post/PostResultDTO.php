<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\Post;

use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Domain\Aggregate\Post;

/**
 * 게시물 조회/생성 결과
 */
final readonly class PostResultDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public UserResultDTO $userResultDTO,
        public ?string $created_at,
        public ?string $updated_at
    ) {}

    /**
     * 도메인 Aggregate를 DTO로 변환하는 팩토리 메서드
     */
    public static function fromAggregate(Post $post, UserResultDTO $userResultDTO): self
    {
        return new self(
            $post->getId(),
            $post->getTitle(),
            $post->getContent(),
            $userResultDTO,
            $post->getCreatedAt(),
            $post->getUpdatedAt()
        );
    }

    /**
     * DTO 인스턴스를 배열로 변환합니다.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            // User DTO도 배열로 변환하여 포함시킵니다.
            'user' => $this->userResultDTO->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}