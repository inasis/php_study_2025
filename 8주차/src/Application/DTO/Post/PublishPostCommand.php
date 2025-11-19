<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\Post;

use Hazelnut\Application\DTO\Validation\Constraints as Assert;

/**
 * 게시물 생성 명령
 */
final readonly class PublishPostCommand
{
    public function __construct(
        #[Assert\NotNull]
        public string $title,
        public string $content,
        public int $authorId
    ) {}
}