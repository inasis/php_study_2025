<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Jwt;

use Hazelnut\Application\Interface\JwtAdapterInterface;
use Hazelnut\Application\UseCase\Jwt\ExtractRefreshTokenUseCase;

final class ExtractRefreshTokenService implements extractRefreshTokenUseCase
{
    public function __construct(
        private JwtAdapterInterface $JwtAdapterInterface
    ) {}

    /**
     * {@inheritDoc}
     */
    public function extractRefreshToken(string $RefreshToken): ?object
    {
        return $this->JwtAdapterInterface->extractRefreshToken($RefreshToken);
    }
}