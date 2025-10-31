<?php
declare(strict_types=1);

namespace Ginger\Repository;

use Ginger\Entity\Post;
use Ginger\Exception\Infrastructure\DatabaseException; 
use Exception;
use Throwable;

class PostRepository
{
    /**
     * @param array $data 
     * @return Post 생성된 Post 객체
     * @throws DatabaseException 데이터베이스 실패 시
     */
    public function create(array $data): Post
    {
        try {
            return Post::create($data); 
        } catch (Throwable $e) {
            // ORM 또는 DB에서 발생한 기술적 예외를 잡아 500 예외로 래핑하여 던집니다.
            throw new DatabaseException("Post 생성 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }
    
    /**
     * @param array $data
     * @return Post|null ID에 해당하는 Post 객체, 없으면 null 반환
     * @throws DatabaseException 데이터베이스 오류 시
     */
    public function read(array $data): ?Post
    {
        try {
            // Post::find는 리소스가 없으면 null을 반환하고, DB 오류 시 예외를 던집니다.
            return Post::find($data['id']);
        } catch (Throwable $e) {
            throw new DatabaseException("Post 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * @param Post $post 업데이트할 기존 Post 엔티티
     * @param array $data 업데이트 데이터
     * @return Post 업데이트된 Post 엔티티
     * @throws DatabaseException 데이터베이스 실패 시
     */
    public function update(Post $post, array $data): Post
    {
        // ID로 글 찾기
        $post = $this->read($data['id']);

        $post->fill($data);
        
        try {
            if (!$post->save()) {
                // save()가 false를 반환하고 예외를 던지지 않은 경우
                throw new Exception("저장 시도 실패: save()가 false를 반환했습니다.");
            }
            return $post;
        } catch (Throwable $e) {
            // ORM/DB 예외나 내부의 일반 Exception을 잡아 래핑합니다
            throw new DatabaseException("Post 수정 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * @param Post $post 삭제할 기존 Post 엔티티
     * @return bool 성공 여부
     * @throws DatabaseException 데이터베이스 실패 시
     */
    public function delete(Post $post): void
    {
        try {
            if (!$post->delete()) {
                throw new Exception("삭제 시도 실패: delete()가 false를 반환했습니다.");
            }
        } catch (Throwable $e) {
            throw new DatabaseException("Post 삭제 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }
}