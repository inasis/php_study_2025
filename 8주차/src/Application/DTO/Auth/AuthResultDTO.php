<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\Auth;

use Hazelnut\Application\DTO\Jwt\JwtResultDTO;
use Hazelnut\Domain\Aggregate\User;

final readonly class AuthResultDTO
{
    public function __construct(
        public ?int $id,
        public string $email,
        public string $name,
        public JwtResultDTO $tokens
    ) {}
    
    /**
     * Domain Aggregate를 DTO로 변환하는 팩토리 메서드
     */
    public static function fromAggregate(User $user, JwtResultDTO $tokens): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getName(),
            $tokens
        );
    }
}