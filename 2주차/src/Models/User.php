<?php
declare(strict_types=1);

namespace Bread\Models;

class User
{
    // private 속성 타입 지정
    private ?int $id;
    private ?string $username;
    private ?string $email;
    private ?string $password;
    private DateTime $createdAt;
    private bool $isActive;

    // 생성자
    public function __construct(?string $username = null, ?string $email = null, ?string $password = null) 
    {
        $this->id = null;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password ? password_hash($password, PASSWORD_DEFAULT) : null;
        $this->createdAt = new DateTime();
        $this->isActive = true;
    }

    // Getter 메서드들
    public function getId(): ?int 
    {
        return $this->id;
    }

    public function getUsername(): ?string 
    {
        return $this->username;
    }

    public function getEmail(): ?string 
    {
        return $this->email;
    }

    public function getCreatedAt(): DateTime 
    {
        return $this->createdAt;
    }

    public function isActive(): bool 
    {
        return $this->isActive;
    }

    // 유효성 검사를 포함한 Setter 메서드들
    public function setId(int $id): void 
    {
        if ($id > 0) {
            $this->id = $id;
        } else {
            throw new InvalidArgumentException("ID는 양의 정수여야 합니다.");
        }
    }

    public function setUsername(string $username): void 
    {
        if (empty($username) || strlen($username) < 3) {
            throw new InvalidArgumentException("사용자명은 3자 이상이어야 합니다.");
        }
        if (strlen($username) > 20) {
            throw new InvalidArgumentException("사용자명은 20자를 초과할 수 없습니다.");
        }
        $this->username = trim($username);
    }

    public function setEmail(string $email): void 
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("유효한 이메일 주소를 입력해주세요.");
        }
        $this->email = strtolower(trim($email));
    }

    public function setPassword(string $password): void 
    {
        if (strlen($password) < 8) {
            throw new InvalidArgumentException("비밀번호는 8자 이상이어야 합니다.");
        }
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setActive(bool $isActive): void 
    {
        $this->isActive = $isActive;
    }

    // 비밀번호 검증 메서드
    public function verifyPassword(string $password): bool 
    {
        return password_verify($password, $this->password ?? '');
    }

    // 사용자 정보 유효성 검사
    public function validate(): array 
    {
        $errors = [];

        if (empty($this->username)) {
            $errors[] = "사용자명이 필요합니다.";
        }

        if (empty($this->email)) {
            $errors[] = "이메일이 필요합니다.";
        }

        if (empty($this->password)) {
            $errors[] = "비밀번호가 필요합니다.";
        }

        return $errors;
    }
}