<?php
declare(strict_types=1);

namespace Ginger\Entity;

use DateTimeInterface;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
interface PostInterface
{
    // 만약 엔티티가 상태를 변경해야 한다면, Setter 대신 명시적인 메서드를 정의할 수 있습니다.
    // public function changeTitle(string $newTitle): void;
    // public function changeContent(string $newContent): void;
}