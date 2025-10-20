<?php
declare(strict_types=1);

namespace Fondue\DTO\Post;

readonly class PostResponseDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content
    ) {}

    /**
     * DTO의 public 속성을 포함하는 배열을 반환합니다.
     * 
     * * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
