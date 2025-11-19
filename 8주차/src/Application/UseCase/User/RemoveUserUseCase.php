<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\User;

/**
 * 사용자 삭제 유스케이스
 */
interface RemoveUserUseCase
{
    public function removeUser(string $userEmail): void;
}