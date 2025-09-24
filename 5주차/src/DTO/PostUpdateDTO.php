<?php
declare(strict_types=1);

namespace Egg\DTO;

use Egg\Validation\Attribute\NotNull;
use Egg\Validation\Attribute\MinLength;

readonly class PostUpdateDTO
{
    public function __construct(
        #[NotNull]
        public int $id,
        #[MinLength(5)]
        public ?string $title,
        public ?string $content
    ) {}
}
