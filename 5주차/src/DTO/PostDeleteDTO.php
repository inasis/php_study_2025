<?php
declare(strict_types=1);

namespace Egg\DTO;

use Egg\Validation\Attribute\NotNull;

readonly class PostDeleteDTO
{
    public function __construct(
        #[NotNull]
        public int $id
    ) {}
}
