<?php
declare(strict_types=1);

namespace Hazelnut\Application\Interface;

/**
 * JWT 생성을 위한 유스케이스
 */
interface JwtAdapterInterface
{
    public function getEmailFromToken(string $token): ?string;

    public function extractAccessToken(string $token): ?object;

    /**
     * Refresh Token을 디코딩하고 유효성 및 타입을 검증합니다.
     *
     * @param string $token Refresh Token 문자열
     * @return object|null 디코딩된 페이로드 객체 또는 유효하지 않거나 타입이 'refresh'가 아닌 경우 null
     */
    public function extractRefreshToken(string $token): ?object;

    public function generateAccessToken(string $email): string;

    /**
     * Refresh Token 생성
     *
     * @param string $email 사용자 이메일
     * @return string 생성된 Refresh Token
     */
    public function generateRefreshToken(string $email): string;

    /**
     * Access Token과 Refresh Token 동시 생성
     *
     * @param string $email 사용자 이메일
     * @return array{
     * access_token: string,
     * refresh_token: string,
     * access_token_expires_in: int,
     * refresh_token_expires_in: int,
     * token_type: 'Bearer'
     * } 토큰 쌍 및 만료 시간 정보
     */
    public function generateTokenPair(string $email): array;

    /**
     * Refresh Token으로 새 Access Token 생성
     *
     * @param string $refreshToken 유효한 Refresh Token
     * @return array|null 갱신된 토큰 쌍 (generateTokenPair와 동일한 구조), 실패 시 null
     */
    public function renewAccessToken(string $refreshToken): ?string;
    
}