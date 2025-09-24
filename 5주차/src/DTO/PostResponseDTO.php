<?php
declare(strict_types=1);

namespace Egg\DTO;

readonly class PostResponseDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content
    ) {}
}
