<?php

namespace Ginger\DTO\User;

use Ginger\DTO\Validation\Attribute\Email;
use Ginger\DTO\Validation\Attribute\NotNull;

class UserReadDTO
{
    public function __construct(
        #[NotNull, Email]
        public string $email
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