<?php
declare(strict_types=1);

namespace Ginger\Service;

use Ginger\Repository\PostRepositoryInterface;
use Ginger\DTO\Post\PostCreateDTO;
use Ginger\DTO\Post\PostReadDTO;
use Ginger\DTO\Post\PostUpdateDTO;
use Ginger\DTO\Post\PostDeleteDTO;
use Ginger\DTO\Post\PostResponseDTO;
use Ginger\Exception\Http\NotFoundException;
use Ginger\Exception\Http\BadRequestException;

class PostService
{
    public function __construct(private readonly PostRepositoryInterface $postRepository) {}

    /**
     * 새 게시물을 생성하고 응답 DTO를 반환합니다.
     * 
     * @param PostCreateDTO $dto
     * @return PostResponseDTO
     */
    public function createPost(PostCreateDTO $dto): PostResponseDTO
    {
        $post = $this->postRepository->create([
            'title' => $dto->title,
            'content' => $dto->content,
        ]);

        return new PostResponseDTO($post->id, $post->title, $post->content);
    }

    /**
     * 특정 게시물을 조회하고 응답 DTO를 반환합니다.
     * 
     * @param \Ginger\DTO\Post\PostReadDTO $dto
     * @return \Ginger\DTO\Post\PostResponseDTO
     * @throws NotFoundException 게시물이 없을 때
     */
    public function readPost(PostReadDTO $dto): PostResponseDTO
    {
        $post = $this->postRepository->read([
            'id' => $dto->id,
        ]);
        
        // 게시물이 없으면 404 예외를 던집니다.
        if (!$post) {
            throw new NotFoundException("ID {$dto->id} 에 해당하는 게시물을 찾을 수 없습니다.");
        };

        return new PostResponseDTO($post->id, $post->title, $post->content);
    }

    /**
     * 특정 게시물을 업데이트하고 응답 DTO를 반환합니다.
     * 
     * @param \Ginger\DTO\Post\PostUpdateDTO $dto
     * @return \Ginger\DTO\Post\PostResponseDTO
     * @throws NotFoundException ID에 해당하는 게시물이 없을 때
     * @throws BadRequestException 업데이트할 내용이 없을 때
     */
    public function updatePost(PostUpdateDTO $dto): PostResponseDTO
    {
        // read()가 Post 객체 또는 null을 반환하여 게시물 존재 여부를 확인할 수 있습니다.
        $post = $this->postRepository->read([
            'id' => $dto->id,
        ]);
        
        if (!$post) {
            throw new NotFoundException("ID {$dto->id} 에 해당하는 게시물을 찾을 수 없습니다.");
        }

        // 업데이트할 데이터를 DTO에서 가져옵니다.
        $dataToUpdate = [];
        if (!empty($dto->title)) {
            $dataToUpdate['title'] = $dto->title;
        }
        if (!empty($dto->content)) {
            $dataToUpdate['content'] = $dto->content;
        }

        if (empty($dataToUpdate)) {
            throw new BadRequestException("업데이트할 내용이 없습니다.");
        }

        // 데이터에 문제가 없다면 업데이트를 실행합니다.
        $updatedPost = $this->postRepository->update($post, $dataToUpdate);

        return new PostResponseDTO(
            $updatedPost->id,
            $updatedPost->title,
            $updatedPost->content
        );
    }

    /**
     * 특정 게시물을 삭제합니다.
     * 
     * @throws NotFoundException 게시물이 없을 때
     */
    public function deletePost(PostDeleteDTO $dto): void
    {
        // read()가 Post 객체 또는 null을 반환하여 게시물 존재 여부를 확인할 수 있습니다.
        $post = $this->postRepository->read([
            'id' => $dto->id,
        ]);

        // 게시물이 없으면 404 예외를 던집니다.
        if (!$post) {
            throw new NotFoundException("ID {$dto->id}에 해당하는 게시물을 찾을 수 없습니다.");
        }

        // 게시물을 삭제합니다
        $isDeleted = $this->postRepository->delete($post);
    }
}