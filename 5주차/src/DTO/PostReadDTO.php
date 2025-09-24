<?php
declare(strict_types=1);

namespace Egg\DTO;

use Egg\Validation\Attribute\NotNull;

readonly class PostReadDTO
{
    public function __construct(
        #[NotNull]
        public int $id
    ) {}
}
