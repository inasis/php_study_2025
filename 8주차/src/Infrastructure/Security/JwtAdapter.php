<?php

namespace Hazelnut\Infrastructure\Security;

use Hazelnut\Application\Interface\JwtAdapterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

/**
 * Firebase\JWT 라이브러리를 사용한 JWT 서비스의 구체적인 구현체
 */
class JwtAdapter implements JwtAdapterInterface
{
    /** @var string $algorithm JWT 서명 알고리즘 */
    private string $algorithm;
    /** @var string $privateKeyPath 개인키 파일 경로 */
    private string $privateKeyPath;
    /** @var string $publicKeyPath 공개키 파일 경로 */
    private string $publicKeyPath;
    /** @var int $accessTokenTtl Access Token 만료 시간 (초) */
    private int $accessTokenTtl;
    /** @var int $refreshTokenTtl Refresh Token 만료 시간 (초) */
    private int $refreshTokenTtl;
    /** @var string $issuer 토큰 발급자 */
    private string $issuer;
    /** @var string|null $privateKey 로드된 개인키 */
    private ?string $privateKey = null;
    /** @var string|null $publicKey 로드된 공개키 */
    private ?string $publicKey = null;

    /**
     * FirebaseJwtTokenService 생성자.
     * 환경 변수에서 설정을 로드하고 키 파일을 로드합니다.
     *
     * @throws Exception 키 파일 로드 실패 시
     */
    public function __construct()
    {
        $this->algorithm = getenv('JWT_ALGORITHM') ?: 'RS256';
        $this->privateKeyPath = getenv('JWT_PRIVATE_KEY_PATH') ?: 'storage/keys/private.key';
        $this->publicKeyPath = getenv('JWT_PUBLIC_KEY_PATH') ?: 'storage/keys/public.key';
        $this->accessTokenTtl = (int)(getenv('JWT_ACCESS_TOKEN_TTL') ?: 900);
        $this->refreshTokenTtl = (int)(getenv('JWT_REFRESH_TOKEN_TTL') ?: 604800);
        $this->issuer = getenv('JWT_ISSUER') ?: 'Ginger';

        $this->loadKeys();
    }

    /**
     * RSA 키 파일 로드
     *
     * @return void
     * @throws Exception 키 파일이 존재하지 않거나 읽을 수 없는 경우
     */
    private function loadKeys(): void
    {
        $privateKeyPath = $this->getBasePath() . $this->privateKeyPath;
        $publicKeyPath = $this->getBasePath() . $this->publicKeyPath;

        if (!file_exists($privateKeyPath)) {
            throw new Exception("Private key not found at: {$privateKeyPath}");
        }

        if (!file_exists($publicKeyPath)) {
            throw new Exception("Public key not found at: {$publicKeyPath}");
        }

        $this->privateKey = file_get_contents($privateKeyPath);
        $this->publicKey = file_get_contents($publicKeyPath);

        if (!$this->privateKey) {
            throw new Exception("Failed to read private key from: {$privateKeyPath}");
        }

        if (!$this->publicKey) {
            throw new Exception("Failed to read public key from: {$publicKeyPath}");
        }
    }

    /**
     * 기본 경로 반환
     *
     * @return string 프로젝트 루트 경로
     */
    private function getBasePath(): string
    {
        return dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    }

    /**
     * {@inheritdoc}
     */
    public function generateAccessToken(string $email): string
    {
        $payload = [
            'iss' => $this->issuer,
            'sub' => $email,
            'email' => $email,
            'iat' => time(),
            'exp' => time() + $this->accessTokenTtl,
            'type' => 'access',
        ];

        return JWT::encode($payload, $this->privateKey, $this->algorithm);
    }

    /**
     * {@inheritdoc}
     */
    public function generateRefreshToken(string $email): string
    {
        $payload = [
            'iss' => $this->issuer,
            'sub' => $email,
            'email' => $email,
            'iat' => time(),
            'exp' => time() + $this->refreshTokenTtl,
            'type' => 'refresh',
        ];

        return JWT::encode($payload, $this->privateKey, $this->algorithm);
    }

    /**
     * {@inheritdoc}
     */
    public function generateTokenPair(string $email): array
    {
        return [
            'access_token' => $this->generateAccessToken($email),
            'refresh_token' => $this->generateRefreshToken($email),
            'access_token_expires_in' => $this->accessTokenTtl,
            'refresh_token_expires_in' => $this->refreshTokenTtl,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function extractToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->publicKey, $this->algorithm));
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function extractAccessToken(string $token): ?object
    {
        $decoded = $this->extractToken($token);
        
        if (!$decoded || (isset($decoded->type) && $decoded->type !== 'access')) {
            return null;
        }

        return $decoded;
    }

    /**
     * {@inheritdoc}
     */
    public function extractRefreshToken(string $token): ?object
    {
        $decoded = $this->extractToken($token);
        
        if (!$decoded || (isset($decoded->type) && $decoded->type !== 'refresh')) {
            return null;
        }

        return $decoded;
    }

    /**
     * {@inheritdoc}
     */
    public function renewAccessToken(string $refreshToken): ?string
    {
        $decoded = $this->extractRefreshToken($refreshToken);
        
        if (!$decoded) {
            return null;
        }

        return $this->generateAccessToken($decoded->email);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailFromToken(string $token): ?string
    {
        $decoded = $this->extractToken($token);
        
        if (!$decoded) {
            return null;
        }

        return $decoded->email ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }
}