<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\User;

/**
 * 사용자 생성 명령
 */
final class RegisterUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $name
    ) {}
}