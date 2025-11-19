<?php
declare(strict_types=1);

namespace Hazelnut\Presentation\Web\Controller;

use Hazelnut\Application\DTO\User\RegisterUserCommand;
use Hazelnut\Application\DTO\User\ModifyUserCommand;
use Hazelnut\Application\DTO\User\UserResultDTO;
use Hazelnut\Application\UseCase\User\RegisterUserUseCase;
use Hazelnut\Application\UseCase\User\RetrieveUserUseCase;
use Hazelnut\Application\UseCase\User\ModifyUserUseCase;
use Hazelnut\Application\UseCase\User\RemoveUserUseCase;
use Hazelnut\Application\Middleware\AuthMiddleware;

class UserController
{
    public function __construct(
        private readonly RegisterUserUseCase $registerUserUseCase,
        private readonly RetrieveUserUseCase $retrieveUserUseCase,
        private readonly ModifyUserUseCase $modifyUserUseCase,
        private readonly RemoveUserUseCase $removeUserUseCase
    ) {}
    
    public function registerUser($params, $requestData): UserResultDTO
    {
        // Application Command 생성
        $registerUserCommand = new RegisterUserCommand(
            email: $requestData['email'],
            password: $requestData['password'],
            name: $requestData['name']
        );

        // Application에서 registerUser Use Case 호출
        $userResultDto = $this->registerUserUseCase->registerUser($registerUserCommand);
     
        // DTO를 반환합니다.
        return $userResultDto;
    }

    public function retrieveUser($params): ?UserResultDTO
    {
        // 미들웨어 결과값에서 유효한 AuthResultDTO을 조회합니다.
        $authResultDTO = $params['middleware'];

        if (!$authResultDTO) {
            return null; // 401 Unauthorized
        }

        if ($authResultDTO->getEmail() !== $params['email']) {
            return null; // 403 Forbidden
        }
        
        // Application에서 retrieveUser Use Case 호출
        $userResultDTO = $this->retrieveUserUseCase->retrieveUser($authResultDTO->email);

        // DTO를 반환합니다.
        return $userResultDTO;
    }

    public function modifyUser($params, $requestData): ?UserResultDTO
    {
        // 미들웨어 결과값에서 유효한 AuthResultDTO을 조회합니다.
        $authResultDTO = $params['middleware'];

        if (!$authResultDTO) {
            return null; // 401 Unauthorized
        }

        if ($authResultDTO->email !== $params['email']) {
            return null; // 403 Forbidden
        }
        
        // Application Command 생성
        $modifyUserCommand = new ModifyUserCommand(
            email: $authResultDTO->email,
            name: $requestData['name'] ?? null,
            password: $requestData['password'] ?? null
        );

        // Application에서 modifyUser Use Case 호출
        $userResultDTO = $this->modifyUserUseCase->modifyUser($modifyUserCommand);

        // DTO를 반환합니다.
        return $userResultDTO;
    }

    public function removeUser($params): bool
    {
        // 미들웨어 결과값에서 유효한 AuthResultDTO을 조회합니다.
        $authResultDTO = $params['middleware'];

        if (!$authResultDTO) {
            return false; // 401 Unauthorized
        }

        if ($authResultDTO->email !== $params['email']) {
            return false; // 403 Forbidden
        }
        
        // Application에서 removeUser Use Case 호출
        $this->removeUserUseCase->removeUser($authResultDTO->email);
        
        return true; // 성공 시 204 No Content와 함께 true 반환 (HTTP 응답에서 처리)
    }
}