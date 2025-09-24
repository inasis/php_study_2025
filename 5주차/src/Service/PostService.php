<?php
declare(strict_types=1);

namespace Egg\Service;

use Egg\Repository\PostRepository;
use Egg\DTO\PostCreateDTO;
use Egg\DTO\PostResponseDTO;
use Egg\DTO\PostReadDTO;
use Egg\DTO\PostUpdateDTO;
use Egg\DTO\PostDeleteDTO;
use Egg\Exception\NotFoundException;
use Egg\Exception\BadRequestException;
use Egg\Exception\InternalServerErrorException;
use Exception;

class PostService
{
    public function __construct(private readonly PostRepository $postRepository) {}

    /**
     * 게시물 생성
     * 
     * @param PostCreateDTO $dto
     * @return PostResponseDTO
     * @throws InternalServerErrorException 게시물 생성에 실패한 경우
     */
    public function createPost(PostCreateDTO $dto): PostResponseDTO
    {
        try {
            $post = $this->postRepository->create([
                'title' => $dto->title,
                'content' => $dto->content
            ]);
            
            if (!$post) {
                throw new InternalServerErrorException("게시물 생성에 실패했습니다.");
            }
            
            return new PostResponseDTO($post->id, $post->title, $post->content);
        } catch (Exception $e) {
            if ($e instanceof InternalServerErrorException) {
                throw $e;
            }
            throw new InternalServerErrorException("게시물 생성 중 오류가 발생했습니다: " . $e->getMessage());
        }
    }
    
    /**
     * 게시물 조회
     * 
     * @param PostReadDTO $dto
     * @return PostResponseDTO
     * @throws NotFoundException 게시물을 찾을 수 없는 경우
     * @throws InternalServerErrorException 조회 중 오류가 발생한 경우
     */
    public function readPost(PostReadDTO $dto): PostResponseDTO
    {
        try {
            $post = $this->postRepository->read($dto->id);
            
            if (!$post) {
                throw new NotFoundException("ID {$dto->id}에 해당하는 게시물을 찾을 수 없습니다.");
            }
            
            return new PostResponseDTO($post->id, $post->title, $post->content);
        } catch (NotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new InternalServerErrorException("게시물 조회 중 오류가 발생했습니다: " . $e->getMessage());
        }
    }

    /**
     * 게시물 수정
     * 
     * @param PostUpdateDTO $dto
     * @return PostResponseDTO
     * @throws NotFoundException 게시물을 찾을 수 없는 경우
     * @throws BadRequestException 업데이트할 내용이 없는 경우
     * @throws InternalServerErrorException 수정 중 오류가 발생한 경우
     */
    public function updatePost(PostUpdateDTO $dto): PostResponseDTO
    {
        try {
            $post = $this->postRepository->findById($dto->id);
            
            if (!$post) {
                throw new NotFoundException("ID {$dto->id}에 해당하는 게시물을 찾을 수 없습니다.");
            }

            $dataToUpdate = [];
            if ($dto->title !== null && $dto->title !== '') {
                $dataToUpdate['title'] = $dto->title;
            }
            if ($dto->content !== null && $dto->content !== '') {
                $dataToUpdate['content'] = $dto->content;
            }
            
            if (empty($dataToUpdate)) {
                throw new BadRequestException("업데이트할 내용이 없습니다. 제목이나 내용 중 최소 하나는 제공되어야 합니다.");
            }
            
            $updatedPost = $this->postRepository->update($post, $dataToUpdate);
            
            if (!$updatedPost) {
                throw new InternalServerErrorException("게시물 업데이트에 실패했습니다.");
            }
            
            // 업데이트된 데이터로 응답 생성
            return new PostResponseDTO(
                $updatedPost->id,
                $updatedPost->title,
                $updatedPost->content
            );
        } catch (NotFoundException | BadRequestException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new InternalServerErrorException("게시물 수정 중 오류가 발생했습니다: " . $e->getMessage());
        }
    }

    /**
     * 게시물 삭제
     * 
     * @param PostDeleteDTO $dto
     * @return void
     * @throws NotFoundException 게시물을 찾을 수 없는 경우
     * @throws InternalServerErrorException 삭제 중 오류가 발생한 경우
     */
    public function deletePost(PostDeleteDTO $dto): void
    {
        try {
            // 먼저 게시물이 존재하는지 확인
            $post = $this->postRepository->read($dto->id);
            
            if (!$post) {
                throw new NotFoundException("ID {$dto->id}에 해당하는 게시물을 찾을 수 없습니다.");
            }
            
            $isDeleted = $this->postRepository->delete($dto->id);
            
            if (!$isDeleted) {
                throw new InternalServerErrorException("게시물 삭제에 실패했습니다.");
            }
        } catch (NotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new InternalServerErrorException("게시물 삭제 중 오류가 발생했습니다: " . $e->getMessage());
        }
    }

    /**
     * 게시물 목록 조회 (페이지네이션 포함)
     * 
     * @param int $page 페이지 번호 (1부터 시작)
     * @param int $limit 페이지당 항목 수
     * @return array
     * @throws BadRequestException 잘못된 페이지 파라미터인 경우
     * @throws InternalServerErrorException 조회 중 오류가 발생한 경우
     */
    public function getPosts(int $page = 1, int $limit = 10): array
    {
        try {
            if ($page < 1) {
                throw new BadRequestException("페이지 번호는 1 이상이어야 합니다.");
            }
            
            if ($limit < 1 || $limit > 20) {
                throw new BadRequestException("페이지당 항목 수는 1~20사이여야 합니다.");
            }
            
            $offset = ($page - 1) * $limit;
            $posts = $this->postRepository->findAll($limit, $offset);
            $totalCount = $this->postRepository->getTotalCount();
            
            $postDTOs = array_map(
                fn($post) => new PostResponseDTO($post->id, $post->title, $post->content),
                $posts
            );
            
            return [
                'posts' => $postDTOs,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ];
        } catch (BadRequestException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new InternalServerErrorException("게시물 목록 조회 중 오류가 발생했습니다: " . $e->getMessage());
        }
    }
}