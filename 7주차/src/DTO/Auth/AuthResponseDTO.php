<?php
declare(strict_types=1);

namespace Ginger\DTO\Auth;

use Ginger\DTO\Validation\Attribute\Email;
use Ginger\DTO\Validation\Attribute\MaxLength;
use Ginger\DTO\Validation\Attribute\MinLength;
use Ginger\DTO\Validation\Attribute\NotNull;

readonly class AuthResponseDTO
{
    public function __construct(
        #[NotNull, Email]
        public string $email,

        #[NotNull, MinLength(2), MaxLength(50)]
        public string $name,

        #[NotNull]
        public AuthTokensDTO $tokens
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