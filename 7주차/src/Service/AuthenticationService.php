<?php
declare(strict_types=1);

namespace Ginger\Service;

use Ginger\DTO\User\UserResponseDTO;
use Ginger\DTO\Auth\AuthLoginDTO;
use Ginger\DTO\Auth\AuthResponseDTO;
use Ginger\DTO\Auth\AuthTokensDTO;
use Ginger\Exception\Http\UnauthorizedException;
use Ginger\Repository\UserRepository;

/**
 * 사용자 인증 및 토큰 관리를 담당하는 서비스 클래스
 */
class AuthenticationService
{
    public function __construct(
        private UserRepository $userRepository,
        private JwtServiceInterface $jwtTokenService, 
    ) {}

    /**
     * 사용자 로그인 및 Access Token 및 Refresh Token을 생성
     * 
     * @param AuthLoginDTO $dto 로그인 요청 데이터
     * @return AuthResponseDTO 인증 응답 DTO (사용자 정보 및 Token Pair 포함)
     * @throws UnauthorizedException 인증 실패 시
     */
    public function login(AuthLoginDTO $dto): AuthResponseDTO
    {
        $user = $this->userRepository->read([
            'email' => $dto->email
        ]);

        // 사용자 검증 및 비밀번호 확인
        if (!$user || !$user->verifyPassword($dto->password)) {
            throw new UnauthorizedException('Invalid email or password');
        }

        // 마지막 로그인 시간 업데이트
        $this->userRepository->update($user, [
            'email' => $dto->email
        ]);

        $tokensArray = $this->jwtTokenService->generateTokenPair($dto->email);

        // 응답 DTO 반환
        return new AuthResponseDTO(
            email: $user->email, 
            name: $user->name,
            tokens: new AuthTokensDTO(
                accessToken: $tokensArray['access_token'],
                refreshToken: $tokensArray['refresh_token']
            )
        );
    }
    
    /**
     * Refresh Token을 사용하여 Access Token 및 Refresh Token을 갱신
     * 
     * @param string $refreshToken 갱신에 사용될 Refresh Token
     * @return AuthTokensDTO 새로운 토큰 쌍 DTO
     * @throws UnauthorizedException 유효하지 않거나 만료된 Refresh Token일 경우
     */
    public function refresh(string $refreshToken): ?AuthTokensDTO
    {
        $newTokenPair = $this->jwtTokenService->refreshAccessToken($refreshToken);

        if (!$newTokenPair) {
            return null;
        }
  
        // 토큰 DTO로 반환
        return new AuthTokensDTO(
            accessToken: $newTokenPair['access_token'], 
            refreshToken: $newTokenPair['refresh_token']
        );
    }

    /**
     * Access Token을 기반으로 현재 로그인된 사용자 정보를 조회
     * 
     * @param string $token 검증된 Access Token
     * @return UserResponseDTO 현재 사용자 정보 DTO
     * @throws UnauthorizedException 토큰이 유효하지 않거나 사용자를 찾을 수 없을 경우
     */
    public function getCurrentUser(string $token): UserResponseDTO
    {
        // 토큰에서 이메일 추출 (유효성 검증 포함)
        $email = $this->jwtTokenService->getEmailFromToken($token);

        if (!$email) {
            throw new UnauthorizedException('Invalid or expired token');
        }

        // 이메일로 사용자 정보 조회
        $user = $this->userRepository->read([
            'email' => $email
        ]);

        if (!$user) {
            // 토큰에 있는 이메일의 사용자가 DB에 존재하지 않을 경우
            throw new UnauthorizedException('User not found');
        }

        // 사용자 정보 DTO 반환
        return new UserResponseDTO($user);
    }
}