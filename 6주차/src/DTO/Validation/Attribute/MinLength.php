<?php
declare(strict_types=1);

namespace Fondue\DTO\Validation\Attribute;

use Fondue\Exception\Validation\ValidationException;
use Attribute;

#[Attribute]
class MinLength
{
    public function __construct(private int $length) {}

    public function validate(string $name, mixed $value): void
    {
        if (strlen((string)$value) < $this->length) {
            throw new ValidationException("{$name} 값은 최소 {$this->length}자 이상이어야 합니다.");
        }
    }
}