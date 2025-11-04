<?php

namespace Ginger\Controller;

use Ginger\DTO\Auth\AuthLoginDTO;
use Ginger\Service\AuthenticationService;
use Ginger\Service\JwtServiceInterface;

class AuthenticationController
{
    /**
     * @param AuthenticationService
     * @param JwtServiceInterface $jwtService
     */
    public function __construct(
        private AuthenticationService $authenticationService,
        private JwtServiceInterface $jwtService
    ) {}
    
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

    /**
     * @param array $vars 경로 변수
     * @param array $requestData 리프레시 토큰 정보가 포함된 요청 본문
     * @return array
     */
    public function refresh($vars, $requestData): ?array
    {
        if (empty($requestData['refreshToken'])) {
             return null;
        }

        return $this->authenticationService->refresh($requestData['refreshToken'])->toArray();
    }

    /**
     * 사용자의 쿠키를 무효화하여 로그아웃을 진행합니다.
     * 
     * @param array $vars 경로 변수
     * @param array $requestData 요청 본문 (필요 없을 수 있으나 구조 통일)
     * @param string|null $accessToken 미들웨어에서 추출되어 주입된 현재 액세스 토큰
     * @return bool
     */
    public function logout($vars, $requestData): array
    {
        $this->invalidateCookie('access_token', '/');
        $this->invalidateCookie('refresh_token', '/token/refresh');

        return [
            'message' => '로그아웃이 완료되었습니다. 모든 토큰 쿠키가 삭제되었습니다.'
        ];
    }

    /**
     * 지정된 쿠키의 만료 시간을 이전으로 돌려 즉시 무효화합니다.
     * 
     * @param string $name 쿠키 이름
     * @param string $path 쿠키 경로, 로그인 시 설정한 경로와 동일해야 합니다
     */
    private function invalidateCookie(string $name, string $path): void
    {
        setcookie(
            $name, 
            '',
            [
                'expires' => time() - 3600, 
                'path' => $path,
                'domain' => null,   // localhost를 위한 환경 설정
                'secure' => false,  // localhost를 위한 환경 설정
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }
}