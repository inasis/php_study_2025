<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\User;

use Hazelnut\Domain\Aggregate\User;

/**
 * 사용자 조회/생성 결과
 */
final class UserResultDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name
    ) {}

    /**
     * Domain Aggregate를 DTO로 변환하는 팩토리 메서드
     */
    public static function fromAggregate(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getEmail(),
            $user->getName()
        );
    }

    /**
     * DTO 인스턴스를 배열로 변환합니다.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
        ];
    }
}