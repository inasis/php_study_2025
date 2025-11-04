<?php
declare(strict_types=1);

namespace Ginger\Repository;

use Ginger\Entity\UserInterface;

/**
 * 사용자 엔티티의 영속성을 관리하는 저장소 인터페이스.
 *
 * 데이터베이스와의 상호작용을 추상화하여 비즈니스 로직과 분리하는 역할을 합니다.
 */
interface UserRepositoryInterface
{
    /**
     * 모든 사용자를 조회합니다.
     * 
     * @return UserInterface[]
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우
     */
    public function getAll(): array;

    /**
     * 사용자 데이터를 기반으로 새로운 사용자를 생성합니다.
     *
     * @param array $data 사용자 생성을 위한 데이터
     * @return UserInterface 새로 생성된 사용자 객체
     * @throws \InvalidArgumentException 필수 데이터가 누락되었거나 유효하지 않을 경우.
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우
     */
    public function create(array $data): UserInterface;

    /**
     * 주어진 이메일을 사용하여 사용자를 검색하고 반환합니다.
     *
     * @param string $email 검색할 사용자의 이메일 주소
     * @return UserInterface|null 사용자를 찾으면 UserInterface 객체를, 찾지 못하면 null을 반환합니다.
     * @throws \Exception 데이터베이스 오류 또는 기타 예상치 못한 오류가 발생했을 경우.
     */
    public function read(string $email): ?UserInterface;

    /**
     * 주어진 사용자 객체의 정보를 업데이트합니다.
     *
     * @param UserInterface $user 업데이트할 사용자 객체
     * @param array $data 업데이트할 필드와 값
     * @return UserInterface 업데이트된 사용자 객체
     * @throws \InvalidArgumentException 업데이트할 데이터가 유효하지 않을 경우
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우
     * @throws \Exception 업데이트 중 데이터베이스 오류 또는 기타 예상치 못한 오류가 발생했을 경우
     */
    public function update(UserInterface $user, array $data): UserInterface;

    /**
     * 주어진 이메일을 가진 사용자를 시스템에서 삭제합니다.
     *
     * @param string $email 삭제할 사용자의 이메일 주소
     * @return bool 사용자 삭제 성공 시 true, 실패 시 false를 반환합니다.
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우
     * @throws \Exception 기타 예상치 못한 오류가 발생했을 경우
     */
    public function delete(string $email): bool;

    /**
     * 주어진 이메일을 가진 사용자의 최종 로그인 시간을 현재 시각으로 업데이트합니다.
     *
     * @param string $email 최종 로그인 시간을 업데이트할 사용자의 이메일 주소.
     * @return bool 업데이트 성공 시 true, 실패 시 false를 반환합니다.
     * @throws DatabaseException 데이터베이스 오류가 발생했을 경우
     */
    public function updateLastLogin(string $email): bool;
}