<?php
declare(strict_types=1);

namespace Hazelnut\Domain\Service;

use Hazelnut\Domain\Aggregate\User;
use Hazelnut\Domain\Repository\UserRepositoryInterface;
use Hazelnut\Domain\Exception;

class UserDomainService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    /**
     * 새 사용자를 생성하고 생성된 User 애그리거트를 반환합니다.
     * 
     * @param string $email
     * @param string $name
     * @param string $password 이미 해시된 비밀번호
     * @return User
     */
    public function registerUser(string $email, string $name, string $password): User
    {
        // 이미 등록된 계정이 있는지 검사
        if ($this->userRepository->findByEmail($email)) {
            throw new Exception\BadRequestException("User already exists.");
        }

        $userAggregate = User::create($email, $name, $password);
        return $this->userRepository->save($userAggregate);
    }

    /**
     * 특정 사용자를 Email로 조회하고 User 애그리거트를 반환합니다.
     * 
     * @param string $email
     * @return User
     */
    public function findUserByEmail(string $email): User
    {
        return $this->userRepository->findByEmail($email);
    }
    
    /**
     * 특정 사용자를 ID로 조회하고 User 애그리거트를 반환합니다.
     * 
     * @param int $id
     * @return User
     */
    public function findUserById(int $id): User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * 특정 사용자를 ID로 조회하고 User 애그리거트 배열을 반환합니다.
     * 
     * @param int $id
     * @return array
     */
    public function findUserByIds(array $ids): array
    {
        return $this->userRepository->findByIds($ids);
    }

    /**
     * 특정 사용자를 업데이트하고 User 애그리거트를 반환합니다.
     * 
     * @param string $email
     * @param ?string $name
     * @param ?string $password
     * @return User
     */
    public function updateUser(string $email, ?string $name, ?string $password): User
    {
        $userAggregate = $this->userRepository->findByEmail($email);
        $userAggregate->update($name, $password);
        return $this->userRepository->save($userAggregate);
    }

    /**
     * 특정 사용자를 삭제합니다.
     * 
     * @param string $email
     * @return void
     */
    public function deleteUser(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);
        $this->userRepository->delete($user);
    }
}