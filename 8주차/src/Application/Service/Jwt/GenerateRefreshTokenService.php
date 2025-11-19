<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Jwt;

use Hazelnut\Application\Interface\JwtAdapterInterface;
use Hazelnut\Application\UseCase\Jwt\GenerateRefreshTokenUseCase;

final class GenerateRefreshTokenService implements GenerateRefreshTokenUseCase
{
    public function __construct(
        private JwtAdapterInterface $JwtAdapterInterface
    ) {}

    /**
     * {@inheritDoc}
     */
    public function generateRefreshToken(string $userEmail): string
    {
        return $this->JwtAdapterInterface->generateRefreshToken($userEmail);
    }
}