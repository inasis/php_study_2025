<?php
declare(strict_types=1);

namespace Fondue\DTO;

use Fondue\DTO\Validation\Attribute\NotNull;

readonly class PostDeleteDTO
{
    public function __construct(
        #[NotNull]
        public int $id
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
