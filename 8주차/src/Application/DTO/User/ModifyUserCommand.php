<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\User;

/**
 * 사용자 수정 명령
 */
final class ModifyUserCommand
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $name,
        public readonly ?string $password
    ) {}
}