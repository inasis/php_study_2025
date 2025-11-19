<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\User;

use Hazelnut\Application\DTO\User\ModifyUserCommand;
use Hazelnut\Application\DTO\User\UserResultDTO;

/**
 * 사용자 수정 유스케이스
 */
interface ModifyUserUseCase
{
    public function modifyUser(ModifyUserCommand $modifyUserCommand): UserResultDTO;
}