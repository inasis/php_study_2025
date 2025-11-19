<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Persistence\Repository;

use Hazelnut\Domain\Aggregate\Post;
use Hazelnut\Domain\Repository\PostRepositoryInterface;
use Hazelnut\Infrastructure\Persistence\Model\Post as PostEloquentModel;
use Hazelnut\Infrastructure\Persistence\Exception;
use \DateTimeInterface;
use Throwable;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private PostEloquentModel $postEloquentModel
    ) {}
    
    /**
     * {@inheritDoc}
     */
    public function save(Post $post): Post
    {        
        try {
            // 애그리거트 ID를 사용하여 Eloquent 모델을 로드하거나 새 모델 인스턴스 생성
            $postModel = $post->getId()
                ? $this->postEloquentModel->find($post->getId())
                : new PostEloquentModel();

            // 도메인 애그리거트의 상태를 Eloquent 모델로 복사 후 데이터베이스 저장
            $postModel->title = $post->getTitle();
            $postModel->content = $post->getContent();
            $postModel->author_id = $post->getAuthorId();
            $postModel->save();

            return $this->toDomain($postModel);
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "Post 저장 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $postId): ?Post
    {
        try {
            $postModel = $this->postEloquentModel->find($postId);
            return $postModel ? $this->toDomain($postModel) : null;
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "Post ID: {$postId} 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findByIds(array $postIds): array
    {
        if (empty($postIds)) {
            return [];
        }

        try {
            $eloquentCollection = $this->postEloquentModel->newQuery()->whereIn('id', $postIds)->get();
            return $eloquentCollection->map(fn ($item) => $this->toDomain($item))->all();
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "Post ID 목록 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Post $post): void
    {
        $id = $post->getId();

        if ($id === null) {
            throw new \InvalidArgumentException("ID가 할당되지 않은 Post 애그리거트는 삭제할 수 없습니다.");
        }
        
        try {
            // ID를 식별자로 사용하여 직접 삭제
            $deletedCount = $this->postEloquentModel->newQuery()->where('id', $id)->delete();
            
            if ($deletedCount === 0) {
                 // 레코드가 없거나 삭제에 실패한 경우
                 throw new \RuntimeException("Post ID {$id} 삭제에 실패했습니다. 레코드를 찾을 수 없습니다.");
            }
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "Post ID {$id} 삭제 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(): array
    {
        try {
            // Eloquent Collection을 가져옵니다.
            $eloquentCollection = $this->postEloquentModel->newQuery()
                ->orderBy('created_at', 'desc')
                ->get();

            return $eloquentCollection
                ->map(fn ($item) => $this->toDomain($item))
                ->all();
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "Post 전체 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 
                500,
                $e
            );
        }
    }
    
    /**
     * Eloquent 모델을 도메인 Post 애그리거트로 변환합니다.
     */
    private function toDomain(PostEloquentModel $eloquentModel): Post
    {
        // Eloquent의 Carbon 인스턴스를 문자열로 변환
        $created_at = $eloquentModel->created_at instanceof \DateTimeInterface
            ? $eloquentModel->created_at->format('Y-m-d H:i:s')
            : (string)$eloquentModel->created_at;

        $updated_at = $eloquentModel->updated_at instanceof \DateTimeInterface
            ? $eloquentModel->updated_at->format('Y-m-d H:i:s')
            : (string)$eloquentModel->updated_at;

        return new Post(
            $eloquentModel->id,
            $eloquentModel->title,
            $eloquentModel->content,
            $eloquentModel->author_id,
            $created_at,
            $updated_at
        );
    }
}