<?php
declare(strict_types=1);

namespace Donut\DTO;

use Donut\Validation\Attribute\NotNull;
use Donut\Validation\Attribute\MinLength;

readonly class PostCreateDTO
{
    public function __construct(
        #[NotNull, MinLength(5)]
        public string $title,
        #[NotNull]
        public string $content
    ) {}
}
