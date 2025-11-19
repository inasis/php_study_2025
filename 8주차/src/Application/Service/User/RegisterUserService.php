<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\User;

use Hazelnut\Application\UseCase\User\RegisterUserUseCase;
use Hazelnut\Application\DTO\User\RegisterUserCommand;
use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Domain\Service\UserDomainService;

/**
 * 사용자 생성 유스케이스 구현체
 */
final class RegisterUserService implements RegisterUserUseCase
{
    public function __construct(
        private readonly UserDomainService $userDomainService
    ) {}

    public function registerUser(RegisterUserCommand $registerUserCommand): UserResultDTO
    {
        // DTO에서 원시 데이터 추출
        $email = $registerUserCommand->email;
        $name = $registerUserCommand->name;
        $password = $registerUserCommand->password;

        // Domain Service 호출
        // Domain Service에 핵심 로직을 위임합니다.
        $userAggregate = $this->userDomainService->registerUser(
            email: $email,
            name: $name,
            password: $password
        );

        // Aggregate를 Application DTO로 변환
        return UserResultDTO::fromAggregate($userAggregate);
    }
}