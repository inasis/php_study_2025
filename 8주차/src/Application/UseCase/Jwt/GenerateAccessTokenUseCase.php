<?php
declare(strict_types=1);

namespace Hazelnut\Application\UseCase\Jwt;

interface GenerateAccessTokenUseCase
{
    /**
     * User Email로 Access Token 생성
     *
     * @param string $userEmail 사용자 이메일
     * @return string 생성된 Access Token
     */
    public function generateAccessToken(string $userEmail): string;
}