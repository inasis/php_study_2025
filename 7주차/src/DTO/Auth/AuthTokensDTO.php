<?php
declare(strict_types=1);

namespace Ginger\DTO\Auth;

use Ginger\DTO\Validation\Attribute\NotNull;

readonly class AuthTokensDTO
{
    public function __construct(
        #[NotNull]
        public string $accessToken,

        #[NotNull]
        public string $refreshToken
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