<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Jwt;

interface GenerateRefreshTokenUseCase
{
    /**
     * User Email로 Refresh Token 생성
     *
     * @param string $userEmail 사용자 이메일
     * @return string 생성된 Refresh Token
     */
    public function generateRefreshToken(string $userEmail): string;
}