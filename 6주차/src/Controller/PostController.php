<?php
declare(strict_types=1);

namespace Fondue\Controller;

use Fondue\Service\PostService;
use Fondue\DTO\PostCreateDTO;
use Fondue\DTO\PostReadDTO;
use Fondue\DTO\PostUpdateDTO;
use Fondue\DTO\PostDeleteDTO;
use Fondue\DTO\Validation\Validator;

class PostController
{
    public function __construct(
        private readonly PostService $postService,
        private readonly Validator $validator
    ) {}

    /**
     * 새 게시글을 생성하고 생성된 데이터를 반환합니다.
     * 
     * @param array $data 요청 본문 데이터 (title, content)
     * @return array 생성된 게시글 데이터
     * @throws \Fondue\Exception\Http\ValidationException 유효성 검사 실패 시
     */
    public function create(array $data = []): array
    {
        $dto = new PostCreateDTO(
            $data['title'] ?? '', 
            $data['content'] ?? ''
        );
        $this->validator->validate($dto);
        return $this->postService->createPost($dto)->toArray();
    }
    
    /**
     * 특정 ID의 게시글을 조회합니다.
     * 
     * @param array $params 쿼리 또는 URL 파라미터 ({'id': int})
     * @return array 조회된 게시글 데이터
     * @throws \Fondue\Exception\Http\ValidationException 유효성 검사 실패 시
     * @throws \Fondue\Exception\Http\NotFoundException 게시글을 찾을 수 없을 때
     */
    public function read(array $params = []): array
    {
        $dto = new PostReadDTO((int)($params['id'] ?? 0));
        $this->validator->validate($dto);
        return $this->postService->readPost($dto)->toArray();
    }
    
    /**
     * 특정 ID의 게시글을 업데이트합니다.
     * 
     * @param array $data 요청 본문 데이터 (id, title, content)
     * @return array 업데이트된 게시글 데이터
     * @throws \Fondue\Exception\Http\ValidationException 유효성 검사 실패 시
     * @throws \Fondue\Exception\Http\NotFoundException 게시글을 찾을 수 없을 때
     */
    public function update(array $data = []): array
    {
        $dto = new PostUpdateDTO(
            (int)($data['id'] ?? 0),
            $data['title'] ?? null,
            $data['content'] ?? null
        );
        $this->validator->validate($dto);
        return $this->postService->updatePost($dto)->toArray();
    }

    /**
     * 특정 ID의 게시글을 삭제합니다.
     * 
     * @param array $data 요청 본문 데이터 (id)
     * @return bool 성공 여부 (HTTP 204 No Content를 위해 호출자에게 boolean 반환)
     * @throws \Fondue\Exception\Http\ValidationException 유효성 검사 실패 시
     * @throws \Fondue\Exception\Http\NotFoundException 게시글을 찾을 수 없을 때
     */
    public function delete(array $data = []): bool
    {
        $dto = new PostDeleteDTO((int)($data['id'] ?? 0));
        $this->validator->validate($dto);
        $this->postService->deletePost($dto);
        
        // 삭제 성공 시 true를 반환하여 호출자가 204 No Content로 응답하도록 유도합니다.
        return true; 
    }
}