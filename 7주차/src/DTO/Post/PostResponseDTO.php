<?php
declare(strict_types=1);

namespace Ginger\DTO\Post;

use Ginger\DTO\Validation\Attribute\NotNull;
use Ginger\DTO\Validation\Attribute\MinLength;

readonly class PostResponseDTO
{
    public function __construct(
        #[NotNull]
        public int $id,

        #[NotNull, MinLength(4)]
        public string $title,
        
        #[NotNull]
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
