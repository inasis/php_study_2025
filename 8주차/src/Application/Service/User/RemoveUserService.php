<?php
declare(strict_types=1);

namespace Hazelnut\Application\Service\User;

use Hazelnut\Application\UseCase\User\RemoveUserUseCase;
use Hazelnut\Domain\Service\UserDomainService;

/**
 * 사용자 삭제 유스케이스 구현체
 */
final class RemoveUserService implements RemoveUserUseCase
{
    public function __construct(
        private readonly UserDomainService $userDomainService
    ) {}

    public function removeUser(string $userEmail): void
    {
        // Domain Service 호출
        // Domain Service에 핵심 로직을 위임합니다.
        $this->userDomainService->deleteUser($userEmail);

        // 삭제는 반환 값이 없습니다.
    }
}