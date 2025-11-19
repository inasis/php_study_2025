<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Jwt;

use Hazelnut\Application\Interface\JwtAdapterInterface;
use Hazelnut\Application\UseCase\Jwt\RenewAccessTokenUseCase;

final class RenewAccessTokenService implements RenewAccessTokenUseCase
{
    public function __construct(
        private JwtAdapterInterface $JwtAdapterInterface
    ) {}

    /**
     * {@inheritDoc}
     */
    public function renewAccessToken(string $refreshToken): ?string
    {
        return $this->JwtAdapterInterface->renewAccessToken($refreshToken);
    }
}