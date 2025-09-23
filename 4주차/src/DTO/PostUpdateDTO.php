<?php
declare(strict_types=1);

namespace Donut\DTO;

use Donut\Validation\Attribute\NotNull;
use Donut\Validation\Attribute\MinLength;

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
