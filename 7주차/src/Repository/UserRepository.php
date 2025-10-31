<?php
declare(strict_types=1);

namespace Ginger\Repository;

use Ginger\Entity\User;
use Ginger\Exception\Infrastructure\DatabaseException; 
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Throwable;

class UserRepository
{
    public function __construct(
        private User $user
    ) {}

    /**
     * 모든 사용자 조회
     * @return Collection|User[]
     */
    public function getAll(): Collection
    {
        // $this->user::all() 이나 $this->user->orderBy(...) 사용
        return $this->user->newQuery()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * 사용자 생성
     */
    public function create(array $data): User
    {
        // fillable 속성을 이용한 대량 할당 (Mass Assignment)
        $user = $this->user->create([
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => $data['passwordHash'],
        ]);

        return $user;
    }

    /**
     * 이메일로 사용자 조회
     * 이메일 존재 여부 확인
     */
    public function read(array $data): ?User
    {
        try {
            // User::find는 리소스가 없으면 null을 반환하고, DB 오류 시 예외를 던집니다.
            return User::find($data['email']);
        } catch (Throwable $e) {
            throw new DatabaseException("User 조회 중 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }
    

    /**
     * 사용자 업데이트
     */
    public function update(User $user, array $data): User
    {
        $user->fill($data);

        try {
            if (!$user->save()) {
                // save()가 false를 반환하고 예외를 던지지 않은 경우
                throw new Exception("저장 시도 실패: save()가 false를 반환했습니다.");
            }
            return $user;
        } catch (Throwable $e) {
            // ORM/DB 예외나 내부의 일반 Exception을 잡아 래핑합니다
            throw new DatabaseException("User 수정 중 데이터베이스 오류가 발생했습니다: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * 사용자 삭제
     */
    public function delete(string $email): bool
    {
        // 쿼리 빌더를 사용하여 직접 삭제
        return $this->user->newQuery()
            ->where('email', $email)
            ->delete() > 0; // 삭제된 행의 개수 반환`
    }

    /**
     * 마지막 로그인 시간 업데이트
     */
    public function updateLastLogin(string $email): bool
    {
        // 쿼리 빌더를 사용하여 updated_at을 업데이트
        $currentTime = date('Y-m-d H:i:s');

        return $this->user->newQuery()
            ->where('email', $email)
            ->update([
                'updated_at' => $currentTime, // updated_at도 함께 업데이트
            ]) > 0;
    }
}