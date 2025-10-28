<?php
declare(strict_types=1);

namespace Ginger\DTO\Auth;

use Ginger\DTO\Validation\Attribute\Email;
use Ginger\DTO\Validation\Attribute\MaxLength;
use Ginger\DTO\Validation\Attribute\NotNull;

// LoginRequest
readonly class AuthLoginDTO
{
    public function __construct(
        #[NotNull, Email]
        public string $email,
        
        // 로그인에서는 비밀번호 복잡성 검증을 할 필요가 없습니다.
        #[NotNull]
        public string $password
    ) {}

    /**
     * DTO의 public 속성을 포함하는 배열을 반환합니다.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}