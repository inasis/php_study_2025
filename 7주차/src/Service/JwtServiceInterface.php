<?php
declare(strict_types=1);

namespace Ginger\Service;

/**
 * JWT 토큰 생성을 위한 애플리케이션 서비스 인터페이스
 */
interface JwtServiceInterface
{
    /**
     * Access Token 생성
     *
     * @param string $email 사용자 이메일
     * @return string 생성된 Access Token
     */
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
     * 토큰 검증
     *
     * @param string $token 검증할 JWT
     * @return object|null 검증 성공 시 디코딩된 페이로드, 실패 시 null
     */
    public function verifyToken(string $token): ?object;

    /**
     * Access Token 검증
     *
     * @param string $token 검증할 Access Token
     * @return object|null 검증 성공 시 디코딩된 페이로드, 실패 시 null
     */
    public function verifyAccessToken(string $token): ?object;

    /**
     * Refresh Token 검증
     *
     * @param string $token 검증할 Refresh Token
     * @return object|null 검증 성공 시 디코딩된 페이로드, 실패 시 null
     */
    public function verifyRefreshToken(string $token): ?object;

    /**
     * Refresh Token으로 새 Access Token 생성
     *
     * @param string $refreshToken 유효한 Refresh Token
     * @return array|null 갱신된 토큰 쌍 (generateTokenPair와 동일한 구조), 실패 시 null
     */
    public function refreshAccessToken(string $refreshToken): ?array;

    /**
     * 토큰에서 email 추출
     *
     * @param string $token JWT
     * @return string|null 추출된 이메일, 실패 시 null
     */
    public function getEmailFromToken(string $token): ?string;

    /**
     * 공개키 반환 (JWKS 엔드포인트용)
     *
     * @return string PEM 형식의 공개키
     */
    public function getPublicKey(): string;

    /**
     * 알고리즘 반환 (JWKS 메타데이터용)
     *
     * @return string JWT 서명 알고리즘
     */
    public function getAlgorithm(): string;
}