<?php

namespace Ginger\Controller;

use Exception;
use Ginger\Service\UserService;
use Ginger\DTO\User\UserReadDTO;
use Ginger\DTO\User\UserCreateDTO;
use Ginger\DTO\User\UserUpdateDTO;
use Ginger\DTO\Auth\AuthLoginDTO;
use Ginger\Controller\Middleware\AuthMiddleware;
use Ginger\Service\AuthenticationService;

class UserController
{
    public function __construct(
        private UserService $userService,
        private AuthenticationService $authService,
        private AuthMiddleware $authMiddleware
    ) {}
    
    // POST /user
    public function create($vars, $requestData): array
    {
        $dto = new UserCreateDTO(
            $requestData['email'] ?? '',
            $requestData['password'] ?? '',
            $requestData['name'] ?? '',
        );

        return $this->userService->createUser($dto)->toArray();
    }

    // GET /user
    public function read($vars): ?array
    {
        $auth = $this->authMiddleware->authenticate();
        var_dump($auth);

        $dto = new UserReadDTO($auth->email);

        if ($auth instanceof \Ginger\Entity\User) {
            if ($dto->email !== $auth->email) {
                return null;
            }

            return $this->userService->readUser($dto)->toArray();
        }
        return null;
    }

    // PUT /user
    public function update($vars, $requestData): ?array
    {
        $targetEmail = $vars['email'];
        $auth = $this->authMiddleware->authenticate();

        if ($auth instanceof \Ginger\Entity\User) {
            if ($targetEmail !== $auth->email) {
                return null;
            }

            $dto = new UserUpdateDTO($auth);
            $dto->email = $targetEmail; 
            $dto->name = $requestData['name'] ?? null;
            $dto->currentPassword = $requestData['current_password'] ?? null;
            $dto->newPassword = $requestData['new_password'] ?? null;

            return $this->userService->updateUser($dto)->toArray();
        }
        return null;
    }

    // DELETE /user
    public function delete($vars): bool
    {
        $targetEmail = $vars['email'];
        $auth = $this->authMiddleware->authenticate();
        
        if ($auth instanceof \Ginger\Entity\User) {
            return true;
        }
        return false;
    }
}