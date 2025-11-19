<?php
declare(strict_types=1);

namespace Hazelnut\Application\Middleware;

use Hazelnut\Application\UseCase\Jwt\ExtractAccessTokenUseCase;
use Hazelnut\Application\UseCase\Jwt\ExtractRefreshTokenUseCase;
use Hazelnut\Application\UseCase\Jwt\RenewAccessTokenUseCase;
use Hazelnut\Application\DTO\Auth\AuthResultDTO;
use Hazelnut\Application\DTO\Jwt\JwtResultDTO;
use Hazelnut\Application\DTO\Jwt\JwtAccessTokenCommand;
use Hazelnut\Application\Exception;
use Hazelnut\Domain\Service\UserDomainService;
use Throwable;

class AuthMiddleware
{
    private string $userEmail;
    private ?string $accessToken;
    private ?string $refreshToken;
    private ?object $accessTokenPayload;
    private ?object $refreshTokenPayload;
    private ?JwtResultDTO $jwtResultDTO;

    public function __construct(
        private readonly UserDomainService $userDomainService,
        private readonly RenewAccessTokenUseCase $renewAccessTokenUseCase,
        private readonly ExtractAccessTokenUseCase $extractAccessTokenUserCase,
        private readonly ExtractRefreshTokenUseCase $extractRefreshTokenUserCase
    ) {}

    /**
     * HTTP Cookie에서 JWT를 추출하고 Jwt Use Case를 사용하여 검증합니다.
     * 
     * @return AuthResultDTO Access Token과 Refesh Token이 포함된 인증된 User 애그리거트
     */
    public function handle(): AuthResultDTO
    {
        // $_COOKIE에서 Access Token, Refresh Token을 추출합니다.
        $this->accessToken = $_COOKIE['access_token'] ?? null;
        $this->refreshToken = $_COOKIE['refresh_token'] ?? null;

        $this->accessTokenPayload = $this->accessToken ?
            $this->extractAccessTokenUserCase->ExtractAccessToken($this->accessToken) :
            null;
        $this->refreshTokenPayload = $this->refreshToken ?
            $this->extractRefreshTokenUserCase->ExtractRefreshToken($this->refreshToken) :
            null;
        
        if (!$this->accessTokenPayload && $this->refreshTokenPayload) {
            $this->accessToken = $this->renewAccessTokenUseCase->renewAccessToken($this->refreshToken);
            $this->accessTokenPayload = $this->extractAccessTokenUserCase->ExtractAccessToken($this->accessToken);
        } else if (!$this->accessTokenPayload && !$this->refreshTokenPayload) {
            throw new Exception\UnauthorizedException('Authentication required: Unauthorized.');
        }
        
        // Access Token Payload에서 이메일을 추출합니다.
        $this->userEmail = $this->accessTokenPayload->email;

        // DB에서 사용자를 찾아 반환합니다.
        $userAggregate = $this->userDomainService->findUserByEmail($this->userEmail);
        if (!$userAggregate) {
            throw new Exception\UnauthorizedException('User specified in token not found.');
        }

        $jwtResultDTO = new JwtResultDTO(
            accessToken: $this->accessToken,
            refreshToken: $this->refreshToken
        );
        
        return AuthResultDTO::fromAggregate($userAggregate, $jwtResultDTO);
    }
}