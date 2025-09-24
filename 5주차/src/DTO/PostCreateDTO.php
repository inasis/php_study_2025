<?php
declare(strict_types=1);

namespace Egg\DTO;

use Egg\Validation\Attribute\NotNull;
use Egg\Validation\Attribute\MinLength;

readonly class PostCreateDTO
{
    public function __construct(
        #[NotNull, MinLength(4)]
        public string $title,
        #[NotNull]
        public string $content
    ) {}
}
