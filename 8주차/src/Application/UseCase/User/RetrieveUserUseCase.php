<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\User;

use Hazelnut\Application\DTO\User\UserResultDTO;

/**
 * 사용자 조회 유스케이스
 */
interface RetrieveUserUseCase
{
    public function retrieveUser(string $userEmail): UserResultDTO;
}