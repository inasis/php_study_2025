<?php

namespace Ginger\DTO\User;

use Ginger\DTO\Validation\Attribute\Email;
use Ginger\DTO\Validation\Attribute\MinLength;
use Ginger\DTO\Validation\Attribute\NotNull;
use Ginger\DTO\Validation\Attribute\PasswordStrength;
use Ginger\DTO\Validation\Attribute\MaxLength;

class UserCreateDTO
{
    public function __construct(
        #[NotNull, Email]
        public string $email,

        #[NotNull, MinLength(8)]
        #[PasswordStrength(message: 'Password must include uppercase, lowercase, and a number.')]
        public string $password,
        
        #[NotNull, MinLength(2), MaxLength(50)]
        public string $name
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