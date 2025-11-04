<?php
declare(strict_types=1);

namespace Ginger\Repository;

use Ginger\Entity\UserInterface;

interface UserRepositoryInterface
{
    /**
     * @return User[]
     */
    public function getAll(): array; // Collection 대신 PHP 배열 반환을 권장
    public function create(array $data): UserInterface;
    public function read(string $email): ?UserInterface; // read 매개변수를 Entity ID로 단순화
    public function update(UserInterface $user, array $data): UserInterface;
    public function delete(string $email): bool;
    public function updateLastLogin(string $email): bool;
}