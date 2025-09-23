<?php
declare(strict_types=1);

namespace Donut\Validation\Attribute;

use Attribute;
use Exception;

#[Attribute]
class MinLength
{
    public function __construct(private int $length) {}

    public function validate(string $name, mixed $value): void
    {
        if (strlen((string)$value) < $this->length) {
            throw new Exception("{$name} 값은 최소 {$this->length}자 이상이어야 합니다.");
        }
    }
}
