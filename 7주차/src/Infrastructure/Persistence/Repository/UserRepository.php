<?php
declare(strict_types=1);

namespace Ginger\Infrastructure\Persistence\Repository;

use Ginger\Repository\UserRepositoryInterface;
use Ginger\Exception\Infrastructure\DatabaseException; 
use Ginger\Entity\UserInterface;
use Ginger\Infrastructure\Persistence\Entity\User as UserEloquentModel;
use Throwable;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserEloquentModel $model
    ) {}

    /**
     * 모든 사용자 조회
     * @return UserInterface[]
     */
    public function getAll(): array
    {
        // Eloquent Collection을 가져와 PHP 배열로 변환
        return $this->model->newQuery()
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    /**
     * 사용자 생성
     */
    public function create(array $data): UserInterface
    {
        // $this->model 인스턴스를 사용하여 find 메서드를 호출합니다.
        // Eloquent의 Primary Key인 'email'을 사용하여 조회합니다.
        $user = $this->model->create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => $data['passwordHash'],
        ]);

        return $user;
    }

    /**
     * 이메일로 사용자 조회
     */
    public function read(string $email): ?UserInterface
    {
        try {
            // 주입된 $this->model을 사용하여 find 호출
            return $this->model->find($email);
        } catch (Throwable $e) {
            throw new DatabaseException("User 조회 중 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }
    
    public function update(UserInterface $user, array $data): UserInterface
    {
        // $user가 실제로 Eloquent Model 인스턴스인지 확인하는 것이 안전합니다.
        if (!($user instanceof UserEloquentModel)) {
             throw new \InvalidArgumentException("제공된 User 엔티티는 Eloquent 모델이 아닙니다.");
        }

        $user->fill($data);

        try {
            if (!$user->save()) {
                throw new Exception("저장 시도 실패: save()가 false를 반환했습니다.");
            }
            return $user;
        } catch (Throwable $e) {
            throw new DatabaseException("User 수정 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * 사용자 삭제
     */
    public function delete(string $email): bool
    {
        try {
            // 주입된 모델의 쿼리 빌더를 사용하여 직접 삭제
            // 삭제된 행의 개수를 반환하며, 1 이상이면 true입니다.
            return $this->model->newQuery()
                ->where('email', $email)
                ->delete() > 0;
        } catch (Throwable $e) {
            throw new DatabaseException("User 삭제 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * 마지막 로그인 시간 업데이트
     */
    public function updateLastLogin(string $email): bool
    {
        $currentTime = date('Y-m-d H:i:s');
        
        try {
            // 주입된 모델의 쿼리 빌더를 사용하여 updated_at을 업데이트
            return $this->model->newQuery()
                ->where('email', $email)
                ->update([
                    // Eloquent의 timestamps가 자동으로 처리하지 않는 필드만 명시적으로 업데이트
                    // created_at, updated_at 필드는 보통 Eloquent가 자동으로 관리하지만, 
                    // 필요에 따라 여기에 명시할 수 있습니다.
                    // 'last_login_at' 필드가 있다면 해당 필드를 업데이트합니다.
                    // 'last_login_at' => $currentTime, 
                ]) > 0;
        } catch (Throwable $e) {
            throw new DatabaseException("업데이트 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }
}