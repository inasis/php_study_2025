<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Jwt;

interface ExtractRefreshTokenUseCase
{
    /**
     * Refresh Token을 디코딩하고 유효성 및 타입을 검증합니다.
     *
     * @param string $token Access Token 문자열
     * @return object|null 디코딩된 페이로드 객체 또는 유효하지 않거나 타입이 'access'가 아닌 경우 null
     */
    public function ExtractRefreshToken(string $VerifyToken): ?object;
}