<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\User;

use Hazelnut\Application\DTO\User\RegisterUserCommand;
use Hazelnut\Application\DTO\User\UserResultDTO;

/**
 * 사용자 생성 유스케이스
 */
interface RegisterUserUseCase
{
    public function registerUser(RegisterUserCommand $registerUserCommand): UserResultDTO;
}