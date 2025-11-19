<?php
declare(strict_types=1);

namespace Hazelnut\Domain\Repository;

use Hazelnut\Domain\Aggregate\Post;
use Hazelnut\Exception\Infrastructure\DatabaseException;

/**
 * 게시물 엔티티의 영속성을 관리하는 저장소 인터페이스.
 */
interface PostRepositoryInterface
{
    /**
     * 모든 게시물을 조회합니다.
     *
     * @return Post[] 게시물 목록
     * @throws DatabaseException 인프라 실패 시 발생.
     */
    public function getAll(): array;

    /**
     * 새로운 게시물 생성 및 기존 게시물 수정을 모두 처리합니다.
     * ID가 없는 Post는 생성, ID가 있는 Post는 수정합니다.
     *
     * @param Post $post 영속화할 Post 애그리거트 객체.
     * @return Post ID가 할당되거나 변경 사항이 반영된 Post 애그리거트 객체.
     * @throws DatabaseException 데이터베이스 저장 작업 중 오류가 발생할 경우.
     */
    public function save(Post $post): Post;

    /**
     * 주어진 고유 ID를 사용하여 특정 게시물을 조회합니다.
     *
     * @param int $id 조회할 게시물의 고유 식별자
     * @return Post|null ID에 해당하는 게시물 객체. 없으면 null을 반환합니다.
     * @throws DatabaseException 데이터베이스 오류 시 발생.
     */
    public function findById(int $id): ?Post;

    /**
     * 주어진 여러 ID를 사용하여 게시물 목록을 조회합니다.
     *
     * @param int[] $ids 조회할 게시물 ID 목록
     * @return Post[] ID에 해당하는 게시물 객체 목록 (ID 순서는 보장되지 않음).
     * @throws DatabaseException 데이터베이스 오류 시 발생.
     */
    public function findByIds(array $ids): array;

    /**
     * 주어진 Post 엔티티 객체를 저장소에서 삭제합니다.
     *
     * @param Post $post 삭제할 Post 애그리거트 객체.
     * @throws DatabaseException 데이터베이스 삭제 작업 중 오류가 발생할 경우.
     */
    public function delete(Post $post): void;
}