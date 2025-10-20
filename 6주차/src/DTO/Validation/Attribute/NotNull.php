<?php
declare(strict_types=1);

namespace Fondue\DTO\Validation\Attribute;

use Fondue\Exception\Validation\ValidationException;
use Attribute;

#[Attribute]
class NotNull
{
    public function validate(string $name, mixed $value): void
    {
        if ($value === null || $value === '') {
            throw new ValidationException("{$name} 값은 null이거나 비어있을 수 없습니다.");
        }
    }
}
