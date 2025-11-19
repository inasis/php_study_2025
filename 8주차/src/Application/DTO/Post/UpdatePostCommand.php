<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\Post;

/**
 * 게시물 수정 명령
 */
final readonly class UpdatePostCommand
{
    public function __construct(
        public int $id,
        public ?string $title,
        public ?string $content
    ) {}
}