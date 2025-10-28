<?php
declare(strict_types=1);

namespace Ginger\DTO\User;

use Ginger\Entity\User;
use Ginger\DTO\Validation\Attribute\Email;
use Ginger\DTO\Validation\Attribute\MaxLength;
use Ginger\DTO\Validation\Attribute\MinLength;
use Ginger\DTO\Validation\Attribute\NotNull;

readonly class UserGetDTO
{
    public function __construct(
        #[NotNull, Email, MaxLength(100)]
        public string $email,

        #[NotNull, MinLength(2), MaxLength(50)]
        public string $name,

        #[NotNull]
        public string $created_at,

        #[NotNull]
        public string $updated_at
    ) {}
    
    // User Entity를 받아 DTO를 쉽게 생성하는 헬퍼 메서드
    public static function fromUserEntity(User $user): self
    {
        return new self(
            email: $user->email,
            name: $user->name,
            created_at: (string) $user->created_at,
            updated_at: (string) $user->updated_at,
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
