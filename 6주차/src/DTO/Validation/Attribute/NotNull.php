<?php
declare(strict_types=1);

namespace Fondue\DTO\Validation\Attribute;

use Attribute;
use Exception;

#[Attribute]
class NotNull
{
    public function validate(string $name, mixed $value): void
    {
        if ($value === null || $value === '') {
            throw new Exception("{$name} 값은 null이거나 비어있을 수 없습니다.");
        }
    }
}
