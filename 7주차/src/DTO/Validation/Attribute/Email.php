<?php
declare(strict_types=1);

namespace Ginger\DTO\Validation\Attribute;

use Ginger\DTO\Validation\ValidatorInterface;
use Attribute;

#[Attribute(\Attribute::TARGET_PROPERTY)]
class Email implements ValidatorInterface 
{
    public function __construct(
        public string $message = '유효한 이메일 형식이 아닙니다.'
    ) {}

    public function validate(string $propertyName, mixed $propertyValue): ?string
    {
        if (!filter_var($propertyValue, FILTER_VALIDATE_EMAIL)) {
            return $this->message;
        }
        return null;
    }
}