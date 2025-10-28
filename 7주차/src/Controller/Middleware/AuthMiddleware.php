<?php
declare(strict_types=1);

namespace Ginger\Controller\Middleware;

use Ginger\Entity\User;
use Ginger\Repository\UserRepositoryInterface;
use Ginger\Service\JwtServiceInterface; // 💡 새로운 의존성
use Throwable;

class AuthMiddleware
{
    private UserRepositoryInterface $userRepository;
    private JwtServiceInterface $jwtService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        JwtServiceInterface $jwtService
    ) {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    /**
     * HTTP Cookie에서 JWT를 추출하고 JwtService를 사용하여 검증합니다.
     * * @return User|array 인증된 User 객체 또는 에러 배열 (401 Unauthorized)
     */
    public function authenticate(): User|array
    {
        // $_COOKIE에서 access_token을 추출합니다.
        $accessToken = $_COOKIE['access_token'] ?? null;
        
        if (!$accessToken) {
            // 토큰이 없는 경우
            return ['error' => 'Authentication required: access_token cookie is missing.', 'code' => 401];
        }

        // JwtServiceInterface를 사용하여 토큰을 검증합니다.
        try {
            $payload = $this->jwtService->verifyAccessToken($accessToken);
            
            if (!$payload) {
                // 토큰 검증 실패 (만료, 서명 오류 등)
                return ['error' => 'Invalid or expired access token.', 'code' => 401];
            }
            
            // 페이로드에서 이메일 추출
            $userEmail = $this->jwtService->getEmailFromToken($accessToken);

            if (!$userEmail) {
                 return ['error' => 'Token payload is missing user identifier.', 'code' => 401];
            }

            // 4. DB에서 사용자를 찾아 반환합니다.
            $user = $this->userRepository->read([
                'email' => $userEmail
            ]);

            if (!$user) {
                return ['error' => 'User specified in token not found.', 'code' => 401];
            }
            
            return $user;
        } catch (Throwable $e) {
            // JWT 라이브러리 내부에서 처리되지 않은 기타 심각한 오류
            return ['error' => 'Authentication processing failed: ' . $e->getMessage(), 'code' => 401];
        }
    }
}