<?php
declare(strict_types=1);

namespace Donut\DTO;

use Donut\Validation\Attribute\NotNull;

readonly class PostReadDTO
{
    public function __construct(
        #[NotNull]
        public int $id
    ) {}
}
