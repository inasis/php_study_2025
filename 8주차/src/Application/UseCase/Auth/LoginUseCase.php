<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Auth;

use Hazelnut\Application\DTO\Auth\AuthLoginCommand;
use Hazelnut\Application\DTO\Auth\AuthResultDTO;

interface LoginUseCase
{
    public function login(AuthLoginCommand $dto): AuthResultDTO;
}