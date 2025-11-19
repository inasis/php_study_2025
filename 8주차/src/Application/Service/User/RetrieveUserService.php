<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\User;

use Hazelnut\Application\UseCase\User\RetrieveUserUseCase;
use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Domain\Service\UserDomainService;

/**
 * 사용자 조회 유스케이스 구현체
 */
final class RetrieveUserService implements RetrieveUserUseCase
{
    public function __construct(
        private readonly UserDomainService $userDomainService
    ) {}

    public function retrieveUser(string $userEmail): UserResultDTO
    {
        // Domain Service 호출
        // Domain Service에 핵심 로직을 위임합니다.
        $userAggregate = $this->userDomainService->findUserByEmail($userEmail);

        // Aggregate를 Application DTO로 변환
        return UserResultDTO::fromAggregate($userAggregate);
    }
}