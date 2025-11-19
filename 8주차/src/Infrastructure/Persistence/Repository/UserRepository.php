<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Persistence\Repository;

use Hazelnut\Domain\Aggregate\User;
use Hazelnut\Domain\Repository\UserRepositoryInterface;
use Hazelnut\Infrastructure\Persistence\Model\User as UserEloquentModel;
use Hazelnut\Infrastructure\Persistence\Exception;
use Throwable;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserEloquentModel $userEloquentModel
    ) {}

    /**
     * {@inheritDoc}
     */
    public function save(User $user): User
    {
        try {
            // 애그리거트 Email을 사용하여 Eloquent 모델을 로드하거나 새 모델 인스턴스 생성
            $attributes = [];
            if ($user->getEmail()) {
                $attributes['email'] = $user->getEmail();
            }

            $userModel = $this->userEloquentModel->newQuery()->firstOrNew($attributes);
            // 도메인 애그리거트의 상태를 Eloquent 모델로 복사 후 데이터베이스 저장
            $userModel->email = $user->getEmail();
            $userModel->name = $user->getName();
            $userModel->password = $user->getPassword();
            $userModel->save();

            return $this->toDomain($userModel);
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "User 저장 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findByEmail(string $email): ?User
    {
        try {
            $userModel = $this->userEloquentModel->newQuery()->where('email', $email)->first();
            return $userModel ? $this->toDomain($userModel) : null;
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "User 이메일 {$email} 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?User
    {
        try {
            $userModel = $this->userEloquentModel->find($id);
            return $userModel ? $this->toDomain($userModel) : null;
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "User ID: {$id} 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findByIds(array $userIds): array
    {
        if(empty($userIds)) {
            return [];
        }

        try {
            $eloquentCollection = $this->userEloquentModel->newQuery()->whereIn('id', $userIds)->get();
            return $eloquentCollection->map(fn ($item) => $this->toDomain($item))->all();
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "User ID 목록 조회 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function delete(User $user): void
    {
        $email = $user->getEmail();
        
        try {
            // 이메일을 식별자로 사용하여 직접 삭제
            $deletedCount = $this->userEloquentModel->newQuery()->where('email', $email)->delete();
            
            if ($deletedCount === 0) {
                 // 레코드가 없거나 삭제에 실패한 경우
                 throw new \RuntimeException("이메일 {$email} 삭제에 실패했습니다. 레코드를 찾을 수 없습니다.");
            }
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "User 삭제 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
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
            $eloquentCollection = $this->userEloquentModel->newQuery()
                ->orderBy('created_at', 'desc')
                ->get();

            return $eloquentCollection
                ->map(fn ($item) => $this->toDomain($item))
                ->all();
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "User 전체 조회 중 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updateLastLogin(string $email): bool
    {
        try {
            // 'last_login_at' 필드를 명시적으로 업데이트
            return $this->userEloquentModel->newQuery()
                ->where('email', $email)
                ->update(['last_login_at' => date('Y-m-d H:i:s')]) > 0;
        } catch (Throwable $e) {
            throw new Exception\DatabaseException(
                "마지막 로그인 시간 업데이트 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * Eloquent 모델을 도메인 User 애그리거트로 변환합니다.
     */
    private function toDomain(UserEloquentModel $eloquentModel): User
    {
        return new User(
            $eloquentModel->id,
            $eloquentModel->email,
            $eloquentModel->name,
            $eloquentModel->password,
            $eloquentModel->created_at,
            $eloquentModel->updated_at
        );
    }
}