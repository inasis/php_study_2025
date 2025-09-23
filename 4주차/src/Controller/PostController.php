<?php
declare(strict_types=1);

namespace Donut\Controller;

use Donut\Service\PostService;
use Donut\DTO\PostCreateDTO;
use Donut\DTO\PostReadDTO;
use Donut\DTO\PostUpdateDTO;
use Donut\DTO\PostDeleteDTO;
use Donut\Validation\Validator;
use Exception;

class PostController
{
    public function __construct(
        private readonly PostService $postService,
        private readonly Validator $validator
    ) {}

    public function create(array $data = []): void
    {
        $this->handleRequest(function() use ($data) {
            $dto = new PostCreateDTO(
                $data['title'] ?? '', 
                $data['content'] ?? ''
            );
            $this->validator->validate($dto);
            $response = $this->postService->createPost($dto);
            return $response;
        });
    }
    
    public function read(array $params = []): void
    {
        $this->handleRequest(function() use ($params) {
            $dto = new PostReadDTO((int)($params['id'] ?? 0));
            $this->validator->validate($dto);
            $response = $this->postService->readPost($dto);
            return $response;
        });
    }
    
    public function update(array $data = []): void
    {
        $this->handleRequest(function() use ($data) {
            $dto = new PostUpdateDTO(
                (int)($data['id'] ?? 0),
                $data['title'] ?? null,
                $data['content'] ?? null
            );
            $this->validator->validate($dto);
            $response = $this->postService->updatePost($dto);
            return $response;
        });
    }

    public function delete(array $data = []): void
    {
        $this->handleRequest(function() use ($data) {
            $dto = new PostDeleteDTO((int)($data['id'] ?? 0));
            $this->validator->validate($dto);
            $this->postService->deletePost($dto);
            return null;
        }, 204);
    }

    /**
     * 공통 요청 처리 메서드
     * 
     * @param callable $action 실행할 비즈니스 로직
     * @param int $successCode 성공 시 HTTP 상태 코드 200
     */
    private function handleRequest(callable $action, int $successCode = 200): void
    {
        try {
            $result = $action();
            
            http_response_code($successCode);
            header('Content-Type: application/json');
            
            if ($successCode === 204) {
                // 204 No Content의 경우 data 없음
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => true, 'data' => $result]);
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Exception 타입에 따른 응답 처리
     */
    private function handleException(Exception $e): void
    {
        $statusCode = match (true) {
            $e instanceof \InvalidArgumentException => 400,
            $e instanceof \Donut\Exception\UnauthorizedException => 401,
            $e instanceof \Donut\Exception\ForbiddenException => 403,
            $e instanceof \Donut\Exception\NotFoundException => 404,
            $e instanceof \Donut\Exception\ValidationException => 422,
            default => 500
        };

        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
    }
}