<?php
declare(strict_types=1);

namespace Ginger\Repository;

use Ginger\Entity\PostInterface;
use Ginger\Exception\Infrastructure\DatabaseException;

/**
 * 게시물 엔티티의 영속성을 관리하는 저장소 인터페이스.
 *
 * 데이터베이스와의 상호작용을 추상화하여 비즈니스 로직과 분리하는 역할을 합니다.
 */
interface PostRepositoryInterface
{
    /**
     * 모든 게시물을 조회합니다.
     *
     * @return PostInterface[] 게시물 목록을 담고 있는 PostInterface 객체의 배열
     * @throws DatabaseException 데이터베이스 연결 오류 또는 쿼리 실행 실패와 같은 인프라 실패 시 발생.
     */
    public function getAll(): array;

    /**
     * 새로운 게시물 데이터를 저장소에 생성하고 영속화합니다.
     *
     * @param array $data 새 게시물 생성을 위한 데이터
     * @return PostInterface 데이터베이스에 성공적으로 저장되고 ID가 할당된 생성된 Post 객체.
     * @throws \InvalidArgumentException 필수 데이터가 누락되었거나 유효하지 않을 경우.
     * @throws DatabaseException 데이터베이스 저장 작업 중 오류가 발생할 경우.
     */
    public function create(array $data): PostInterface;

    /**
     * 주어진 고유 ID를 사용하여 특정 게시물을 조회합니다.
     *
     * @param int $id 조회할 게시물의 고유 식별자
     * @return PostInterface|null ID에 해당하는 게시물 객체. 해당 ID의 게시물이 없으면 null을 반환합니다.
     * @throws DatabaseException 데이터베이스 연결 또는 조회 쿼리 실행 중 오류가 발생할 경우.
     */
    public function read(int $id): ?PostInterface;

    /**
     * 기존 게시물 엔티티의 정보를 주어진 데이터로 업데이트하고 영속화합니다.
     *
     * @param PostInterface $post 데이터베이스에 업데이트할 기존 Post 엔티티 객체.
     * @param array $data 업데이트할 필드와 값을 포함하는 배열
     * @return PostInterface 성공적으로 업데이트된 후의 Post 엔티티 객체.
     * @throws \InvalidArgumentException 업데이트할 데이터가 유효하지 않거나 빈 배열일 경우.
     * @throws DatabaseException 데이터베이스 업데이트 작업 중 오류가 발생할 경우.
     */
    public function update(PostInterface $post, array $data): PostInterface;

    /**
     * 주어진 고유 ID에 해당하는 게시물을 저장소에서 삭제합니다.
     *
     * @param int $id 삭제할 게시물의 고유 식별자
     * @return bool 삭제 성공 시 true를 반환합니다. 삭제할 대상을 찾지 못했거나 삭제에 실패하면 false를 반환합니다.
     * @throws DatabaseException 데이터베이스 삭제 작업 중 오류가 발생할 경우.
     */
    public function delete(int $id): bool;
}