<?php
declare(strict_types=1);

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

    // Setter 메서드들 (유효성 검사 포함)
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

    // 사용자 정보를 배열로 반환 (비밀번호 제외)
    public function toArray(): array 
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'is_active' => $this->isActive
        ];
    }

    // 사용자 정보를 JSON으로 반환
    public function toJson(): string 
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
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

    // 사용자 정보 출력 (디버그용)
    public function displayInfo(): void 
    {
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
        echo "<h3>사용자 정보</h3>";
        echo "<p><strong>ID:</strong> " . ($this->id ?? 'N/A') . "</p>";
        echo "<p><strong>사용자명:</strong> " . htmlspecialchars($this->username ?? 'N/A') . "</p>";
        echo "<p><strong>이메일:</strong> " . htmlspecialchars($this->email ?? 'N/A') . "</p>";
        echo "<p><strong>생성일:</strong> " . $this->createdAt->format('Y-m-d H:i:s') . "</p>";
        echo "<p><strong>활성화:</strong> " . ($this->isActive ? '예' : '아니오') . "</p>";
        echo "</div>";
    }

    // 정적 메서드: 배열에서 User 객체 생성
    public static function fromArray(array $data): self 
    {
        $user = new self();

        if (isset($data['id'])) $user->setId((int)$data['id']);
        if (isset($data['username'])) $user->setUsername((string)$data['username']);
        if (isset($data['email'])) $user->setEmail((string)$data['email']);
        if (isset($data['password'])) $user->setPassword((string)$data['password']);
        if (isset($data['is_active'])) $user->setActive((bool)$data['is_active']);
        if (isset($data['created_at'])) {
            $user->createdAt = new DateTime($data['created_at']);
        }

        return $user;
    }
}
