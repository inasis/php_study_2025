<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Jwt;

use Hazelnut\Application\Interface\JwtAdapterInterface;
use Hazelnut\Application\UseCase\Jwt\ExtractAccessTokenUseCase;

final class ExtractAccessTokenService implements extractAccessTokenUseCase
{
    public function __construct(
        private JwtAdapterInterface $JwtAdapterInterface
    ) {}

    /**
     * {@inheritDoc}
     */
    public function extractAccessToken(string $accessToken): ?object
    {
        return $this->JwtAdapterInterface->extractAccessToken($accessToken);
    }
}