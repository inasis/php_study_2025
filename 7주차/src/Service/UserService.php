<?php
declare(strict_types=1);

namespace Ginger\Service;

use Ginger\Repository\UserRepositoryInterface;
use Ginger\DTO\User\UserCreateDTO;
use Ginger\DTO\User\UserReadDTO;
use Ginger\DTO\User\UserUpdateDTO;
use Ginger\DTO\User\UserDeleteDTO;
use Ginger\DTO\User\UserResponseDTO;
use Ginger\Exception\Http\BadRequestException;
use Ginger\Exception\Runtime\UserNotFoundException;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private JwtServiceInterface $jwtService
    ) {}

    /**
     * 새 사용자를 생성하고 응답 DTO를 반환합니다.
     *
     * @param UserCreateDTO $dto 사용자 데이터를 받습니다.
     * @return UserResponseDTO
     */
    public function createUser(UserCreateDTO $dto): UserResponseDTO
    {
        $user = $this->userRepository->read([
            'email' => $dto->email,
        ]);

        if ($user) {
            throw new BadRequestException("User already exists.");
        }

        $passwordHash = password_hash($dto->password, PASSWORD_BCRYPT, ['cost' => 12]);

        $create = $this->userRepository->create([
            'email' => $dto->email,
            'name' => $dto->name,
            'passwordHash' => $passwordHash
        ]);

        return new UserResponseDTO($create);
    }

    /**
     * 특정 사용자를 조회하고 응답 DTO를 반환합니다.
     */
    public function readUser(UserReadDTO $dto): UserResponseDTO
    {
        $user = $this->userRepository->read([
            'email' => $dto->email,
        ]);

        if (!$user) {
            throw new UserNotFoundException("User not found");
        }

        return new UserResponseDTO($user);
    }

    /**
     * 특정 사용자를 업데이트하고 응답 DTO를 반환합니다.
     * 
     * @param \Ginger\DTO\User\UserUpdateDTO $dto
     * @return \Ginger\DTO\User\UserResponseDTO
     */
    public function updateUser(UserUpdateDTO $dto): UserResponseDTO
    {
        $user = $this->userRepository->read(['email' => $dto->email]);
        
        if (!$user) {
            throw new UserNotFoundException("User not found");
        }
        
        $updateData = [];

        // 업데이트할 데이터를 DTO에서 가져옵니다.
        $updateData['name'] = $dto->name;
        
        if (isset($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            unset($data['password']);
        }
        
        // 데이터에 문제가 없다면 업데이트를 실행합니다.
        $updateUser = $this->userRepository->update($user, $updateData);

        // 응답 DTO 반환
        return new UserResponseDTO($updateUser);
    }

    /**
     * 특정 사용자를 삭제합니다.
     */
    public function deleteUser(UserDeleteDTO $dto): void
    {
        $user = $this->userRepository->read(['email' => $dto->email]);

        if (!$user) {
            throw new UserNotFoundException("User not found");
        }

        $this->userRepository->delete($user->email);
    }
}