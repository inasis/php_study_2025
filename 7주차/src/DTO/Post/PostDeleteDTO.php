<?php
declare(strict_types=1);

namespace Ginger\DTO\Post;

use Ginger\DTO\Validation\Attribute\NotNull;

readonly class PostDeleteDTO
{
    public function __construct(
        #[NotNull]
        public int $id
    ) {}
}
