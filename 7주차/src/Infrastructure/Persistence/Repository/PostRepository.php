<?php
declare(strict_types=1);

namespace Ginger\Infrastructure\Persistence\Repository;

use Ginger\Repository\PostRepositoryInterface;
use Ginger\Infrastructure\Persistence\Entity\Post as PostEloquentModel;
use Ginger\Entity\PostInterface;
use Ginger\Exception\Infrastructure\DatabaseException; 
use Throwable;
use Exception;

class PostRepository implements PostRepositoryInterface
{
    // Post Eloquent Model을 의존성 주입으로 받습니다.
    public function __construct(
        private PostEloquentModel $model
    ) {}
    
    public function getAll(): array
    {
        try {
            // Eloquent Collection을 가져와 PHP 배열로 변환하여 PostInterface[]로 반환합니다.
            return $this->model->newQuery()
                ->orderBy('created_at', 'desc')
                ->get()
                ->all();
        } catch (Throwable $e) {
            throw new DatabaseException("Post 전체 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): PostInterface
    {
        try {
            // $this->model을 사용하여 새 인스턴스를 생성합니다.
            $post = $this->model->create([
                'title' => $data['title'],
                'content' => $data['content'],
            ]);
            
            return $post; 
        } catch (Throwable $e) {
            // ORM 또는 DB에서 발생한 기술적 예외를 잡아 래핑하여 던집니다.
            throw new DatabaseException("Post 생성 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }
    
    public function read(int $id): ?PostInterface
    {
        try {
            // $this->model을 사용하여 Primary Key로 조회합니다.
            return $this->model->find($id);
        } catch (Throwable $e) {
            throw new DatabaseException("Post ID: {$id} 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }

    public function update(PostInterface $post, array $data): PostInterface
    {
        // $userRepository에서와 같이, 인터페이스를 구현한 구체 클래스인지 확인합니다.
        if (!($post instanceof PostEloquentModel)) {
             throw new \InvalidArgumentException("제공된 Post 엔티티는 Eloquent 모델 인스턴스가 아닙니다.");
        }
        
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

    public function delete(int $id): bool
    {
        try {
            // $this->model의 쿼리 빌더를 사용하여 직접 삭제
            // 삭제된 행의 개수를 반환하며, 1 이상이면 true입니다.
            return $this->model->newQuery()
                ->where('id', $id)
                ->delete() > 0;
        } catch (Throwable $e) {
            throw new DatabaseException("Post 삭제 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }
}