<?php

namespace Ginger\DTO\User;

use Ginger\DTO\Validation\Attribute\Email;
use Ginger\DTO\Validation\Attribute\MinLength;
use Ginger\DTO\Validation\Attribute\NotNull;
use Ginger\DTO\Validation\Attribute\PasswordStrength;
use Ginger\DTO\Validation\Attribute\MaxLength;

class UserUpdateDTO
{
    public function __construct(
        #[NotNull, Email]
        public string $email,
        
        #[MaxLength(100)]
        public ?string $name = null,

        #[MinLength(8), PasswordStrength]
        public ?string $newPassword = null,
        public ?string $currentPassword = null
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