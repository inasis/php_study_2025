<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Jwt;

use Hazelnut\Application\Interface\JwtAdapterInterface;
use Hazelnut\Application\UseCase\Jwt\GenerateAccessTokenUseCase;

final class GenerateAccessTokenService implements GenerateAccessTokenUseCase
{
    public function __construct(
        private JwtAdapterInterface $JwtAdapterInterface
    ) {}

    /**
     * {@inheritDoc}
     */
    public function generateAccessToken(string $userEmail): string
    {
        return $this->JwtAdapterInterface->generateAccessToken($userEmail);
    }
}