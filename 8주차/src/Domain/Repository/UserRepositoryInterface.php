<?php
declare(strict_types=1);

namespace Hazelnut\Domain\Repository;

use Hazelnut\Domain\Aggregate\User;
use Hazelnut\Exception\Infrastructure\DatabaseException;

/**
 * 사용자 애그리거트의 영속성을 관리하는 저장소 인터페이스.
 */
interface UserRepositoryInterface
{
    /**
     * 모든 사용자를 조회하고 User 애그리거트 객체의 배열로 반환합니다.
     * * @return User[]
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우
     */
    public function getAll(): array;

    /**
     * 새로운 사용자 애그리거트를 저장하거나 기존 사용자 애그리거트의 변경 사항을 영속화합니다.
     * 
     * ID가 없는 User 객체는 생성하고, ID가 있는 User 객체는 수정합니다.
     *
     * @param User $user 영속화할 User 애그리거트 객체
     * @return User ID가 할당되거나 변경 사항이 반영된 User 애그리거트 객체
     * @throws DatabaseException 데이터베이스 저장 작업 중 오류가 발생할 경우.
     */
    public function save(User $user): User;

    /**
     * 주어진 이메일을 사용하여 사용자를 검색하고 User 애그리거트 객체를 반환합니다.
     *
     * @param string $email 검색할 사용자의 이메일 주소
     * @return User|null 사용자를 찾으면 User 애그리거트 객체를, 찾지 못하면 null을 반환합니다.
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우.
     */
    public function findByEmail(string $email): ?User;

    /**
     * 주어진 고유 ID를 사용하여 특정 사용자를 조회하고 User 애그리거트 객체를 반환합니다.
     *
     * @param int $id 조회할 사용자의 고유 식별자
     * @return User|null ID에 해당하는 사용자 객체. 없으면 null을 반환합니다.
     * @throws DatabaseException 데이터베이스 오류 시 발생.
     */
    public function findById(int $id): ?User;

    /**
     * 주어진 여러 ID를 사용하여 사용자 목록을 조회하고 User 애그리거트 객체의 배열로 반환합니다.
     *
     * @param int[] $ids 조회할 사용자 ID 목록
     * @return User[] ID에 해당하는 사용자 객체 목록 (ID 순서는 보장되지 않음).
     * @throws DatabaseException 데이터베이스 오류 시 발생.
     */
    public function findByIds(array $ids): array;

    /**
     * 주어진 User 애그리거트 객체를 시스템에서 삭제합니다.
     *
     * @param User $user 삭제할 User 애그리거트 객체
     * @throws DatabaseException 데이터베이스 삭제 작업 중 오류가 발생했을 경우.
     */
    public function delete(User $user): void;

    /**
     * 주어진 이메일을 가진 사용자의 최종 로그인 시간을 현재 시각으로 업데이트합니다.
     *
     * @param string $email 최종 로그인 시간을 업데이트할 사용자의 이메일 주소.
     * @return bool 업데이트 성공 시 true, 실패 시 false를 반환합니다.
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우
     */
    public function updateLastLogin(string $email): bool;
}