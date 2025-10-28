<?php
declare(strict_types=1);

namespace Ginger\Repository;

use Ginger\Entity\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * 모든 사용자 조회
     * @return Collection|User[]
     */
    public function getAll(): Collection;

    /**
     * 사용자 생성
     */
    public function create(array $data): User;

    /**
     * 이메일로 사용자 조회
     */
    public function read(array $data): ?User;

    /**
     * 사용자 업데이트
     */
    public function update(User $user, array $data): User;

    /**
     * 사용자 삭제
     */
    public function delete(string $email): bool;

    /**
     * 마지막 로그인 시간 업데이트
     */
    public function updateLastLogin(string $email): bool;
}