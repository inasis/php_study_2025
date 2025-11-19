<?php
declare(strict_types=1);

namespace Hazelnut\Domain\Service;

use Hazelnut\Domain\Aggregate\Post;
use Hazelnut\Domain\Repository\PostRepositoryInterface;

class PostDomainService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {}

    /**
     * 새 게시물을 생성하고 Post 애그리거트를 반환합니다.
     * 
     * @param string $title
     * @param string $content
     * @return Post
     */
    public function createPost(string $title, string $content, int $authorId): Post
    {
        $postAggregate = Post::create($title, $content, $authorId);
        return $this->postRepository->save($postAggregate);
    }
    
    /**
     * 게시물 ID를 받아 Post 애그리거트를 반환합니다.
     * 
     * @param int $id
     * @return Post
     */
    public function findPostById(int $postId): Post
    {
        return $this->postRepository->findById($postId);
    }

    /**
     * 게시물 ID 배열을 받아 Post 애그리거트 배열을 반환합니다.
     * 
     * @param int $id
     * @return Post
     */
    public function findPostByIds(array $postIds): array
    {
        return $this->postRepository->findByIds($postIds);
    }


    /**
     * 특정 게시물을 업데이트하고 Post 애그리거트를 반환합니다.
     * 
     * @param int $id
     * @param ?string $title
     * @param ?string $content
     * @return Post
     */
    public function updatePost(int $id, ?string $title, ?string $content): Post
    {
        $postAggregate = $this->findPostById($id);
        $postAggregate->update($title, $content);
        return $this->postRepository->save($postAggregate);
    }

    /**
     * 특정 게시물을 삭제합니다.
     * 
     * @param int $id
     */
    public function deletePost(int $id): void
    {
        $post = $this->findPostById($id);
        $this->postRepository->delete($post);
    }

}