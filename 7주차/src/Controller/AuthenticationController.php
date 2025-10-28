<?php

namespace Ginger\Controller;

use Ginger\DTO\Auth\AuthLoginDTO;
use Ginger\Service\AuthenticationService;
use Ginger\Service\JwtServiceInterface;
use Ginger\Repository\UserRepositoryInterface;

class AuthenticationController
{
    /**
     * @param AuthenticationService
     * @param UserRepositoryInterface $userRepository
     * @param JwtServiceInterface $jwtService
     */
    public function __construct(
        private AuthenticationService $authenticationService,
        private UserRepositoryInterface $userRepository,
        private JwtServiceInterface $jwtService
    ) {}
    
    // POST /auth/login
    /**
     * @param array $vars FastRoute 등에서 넘어온 URI
     * @param array $requestData 라우터/미들웨어에서 JSON 본문을 파싱하여 주입한 데이터
     * @return array ['data' => ..., 'status' => ...] 형태의 응답 배열
     */
    public function login($vars, $requestData): array
    {
        // DTO 생성
        $dto = new AuthLoginDTO(
            $requestData['email'] ?? '', 
            $requestData['password'] ?? ''
        );

        return $this->authenticationService->login($dto)->toArray();
    }

    // POST /auth/refresh
    /**
     * @param array $vars 경로 변수
     * @param array $requestData 리프레시 토큰 정보가 포함된 요청 본문
     * @return array
     */
    public function refresh($vars, $requestData): ?array
    {
        $refreshToken = $requestData['refreshToken'] ?? null;
        
        if (empty($refreshToken)) {
             return null;
        }

        return $this->authenticationService->refresh($refreshToken)->toArray();

        if (is_array($result) && isset($result['error'])) {
            // 토큰 만료 또는 유효하지 않음
            return null;
        }
    }

    // POST /auth/logout
    /**
     * @param array $vars 경로 변수
     * @param array $requestData 요청 본문 (필요 없을 수 있으나 구조 통일)
     * @param string|null $accessToken 미들웨어에서 추출되어 주입된 현재 액세스 토큰
     * @return bool
     */
    public function logout($vars, $requestData): array
    {
        // 1. accessToken 쿠키 무효화
        $this->invalidateCookie('access_token', '/');
        
        // 2. refreshToken 쿠키 무효화
        $this->invalidateCookie('refresh_token', '/token/refresh');

        return [
            'message' => '로그아웃이 완료되었습니다. 모든 토큰 쿠키가 삭제되었습니다.'
        ];
    }

    /**
     * 지정된 쿠키를 즉시 만료시켜 무효화합니다.
     * @param string $name 쿠키 이름
     * @param string $path 쿠키 경로 (로그인 시 설정한 경로와 동일해야 함)
     */
    private function invalidateCookie(string $name, string $path): void
    {
        // 쿠키의 만료 시간을 현재 시각보다 훨씬 이전으로 설정합니다 (예: 1시간 전).
        $pastTime = time() - 3600; 

        setcookie(
            $name, 
            '', // 값을 비웁니다.
            [
                'expires' => $pastTime, 
                'path' => $path,
                'domain' => null,   // localhost 환경 설정 유지
                'secure' => false,  // localhost 환경 설정 유지
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }
}