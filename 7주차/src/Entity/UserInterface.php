<?php
declare(strict_types=1);

namespace Ginger\Entity;

/**
 * @property string $email
 * @property string $name
 * @property string $password
 * @property DateTimeInterface $created_at
 * @property DateTimeInterface $updated_at
 */
interface UserInterface
{
    public function getEmail(): string;
    public function getName(): string;
    public function verifyPassword(string $password): bool;
    public function getCreatedAt(): \DateTimeInterface;
    public function getUpdatedAt(): \DateTimeInterface;

    // 만약 엔티티가 상태를 변경해야 한다면, Setter 대신 명시적인 메서드를 정의할 수 있습니다.
    // public function changeName(string $newName): void;
    // public function changePassword(string $newPlainPassword): void;
}