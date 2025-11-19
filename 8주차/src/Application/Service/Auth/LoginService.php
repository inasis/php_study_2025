<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\Auth;

use Hazelnut\Application\UseCase\Auth\LoginUseCase;
use Hazelnut\Application\UseCase\Jwt\GenerateAccessTokenUseCase;
use Hazelnut\Application\UseCase\Jwt\GenerateRefreshTokenUseCase;
use Hazelnut\Application\DTO\Auth\AuthLoginCommand;
use Hazelnut\Application\DTO\Auth\AuthResultDTO;
use Hazelnut\Application\DTO\Jwt\JwtResultDTO;
use Hazelnut\Application\Exception;
use Hazelnut\Domain\Repository\UserRepositoryInterface;

final class LoginService implements LoginUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GenerateAccessTokenUseCase $generateAccessTokenUseCase,
        private GenerateRefreshTokenUseCase $generateRefreshTokenUseCase
    ) {}

    /**
     * 사용자 로그인 및 Access Token 및 Refresh Token을 생성
     *
     * @param AuthLoginCommand $dto 로그인 요청 데이터
     * @return AuthResultDTO 인증 응답 DTO (사용자 정보 및 Token Pair 포함)
     * @throws Exception\UnauthorizedException 인증 실패 시
     */
    public function login(AuthLoginCommand $authLoginCommand): AuthResultDTO
    {
        // 사용자 조회 및 검증
        $user = $this->userRepository->findByEmail($authLoginCommand->email);

        if (!$user || !$user->verifyPassword($authLoginCommand->password)) {
            throw new Exception\UnauthorizedException('Invalid email or password');
        }

        // 부가적인 인프라 로직 처리 (최근 로그인 시간 업데이트)
        // 참고: 이 로직은 인프라 계층의 UserRepository 구현체에서 처리되거나, 
        // 도메인 서비스(예: UserDomainService)를 통해 처리하는 것이 더 클린할 수 있습니다.
        $this->userRepository->updateLastLogin($authLoginCommand->email);

        // JWT 토큰 생성
        $tokensArray['access_token'] = $this->generateAccessTokenUseCase
            ->generateAccessToken($authLoginCommand->email);
        $tokensArray['refresh_token'] = $this->generateRefreshTokenUseCase
            ->generateRefreshToken($authLoginCommand->email);

        // 응답 DTO 반환
        return new AuthResultDTO(
            id: $user->getId(),
            email: $user->getEmail(),
            name: $user->getName(),
            tokens: new JwtResultDTO(
                accessToken: $tokensArray['access_token'],
                refreshToken: $tokensArray['refresh_token']
            )
        );
    }
}