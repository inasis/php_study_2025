<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\Jwt;

final readonly class JwtResultDTO
{
    public function __construct(
        public string $accessToken,
        public string $refreshToken
    ) {}
}