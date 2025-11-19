<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Jwt;

interface RenewAccessTokenUseCase
{
    public function renewAccessToken(string $refreshToken): ?string;
}