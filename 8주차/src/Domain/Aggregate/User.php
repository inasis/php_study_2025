<?php
declare(strict_types=1);

namespace Hazelnut\Domain\Aggregate;

/**
 * 사용자의 상태와 변경 로직을 캡슐화합니다.
 * 
 * @method int|null getId()
 * @method string getEmail()
 * @method string getName()
 * @method string getPassword()
 * @method \DateTimeInterface|null getCreatedAt()
 * @method \DateTimeInterface|null getUpdatedAt()
 */
class User
{
    public function __construct(
        private ?int $id,
        private string $email,
        private string $name,
        private string $password,
        private ?\DateTimeInterface $createdAt,
        private ?\DateTimeInterface $updatedAt
    ) {}

    public static function create(string $email, string $name, string $password): self
    {
        // 새 게시물 생성 시 ID와 시간은 아직 할당되지 않음
        return new self(null, $email, $name, $password, null, null);
    }
    
    // 사용자 정보 업데이트를 캡슐화합니다.
    public function update(?string $name, ?string $password): void
    {
        if ($name !== null) { 
            $this->name = $name;
        }
        
        if ($password !== null) {
            $this->password = $password;
        }
    }

    /**
     * 주어진 비밀번호가 저장된 해시와 일치하는지 확인합니다.
     *
     * @param string $plainPassword 사용자가 입력한 평문 비밀번호
     * @return bool 일치하면 true, 아니면 false
     */
    public function verifyPassword(string $password): bool
    {
        // $this->password는 모델의 현재 속성 해시값을 가져옵니다.
        return password_verify($password, $this->password);
    }


    public function __call($method, $args)
    {
        // get/set으로 시작하는 메서드인지 확인
        if (!str_starts_with($method, 'get') && !str_starts_with($method, 'set')) {
            throw new \BadMethodCallException("Method $method does not exist");
        }

        // 접두사 제거
        $prefix = substr($method, 0, 3);
        $property = substr($method, 3);

        // 프로퍼티 이름이 비어있으면 에러
        if (empty($property)) {
            throw new \BadMethodCallException("Invalid {$prefix}ter method name");
        }

        $property = lcfirst($property);

        // 프로퍼티가 camelCase로 존재하는 경우 snake_case로 변환합니다.
        $property = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $property));

        // 프로퍼티 존재 여부 확인
        if (!property_exists($this, $property)) {
            throw new \BadMethodCallException("Property $property does not exist");
        }

        if ($prefix === 'get') {
            // getter 처리
            return $this->$property;
        } elseif ($prefix === 'set') {
            // setter 처리
            if (count($args) < 1) {
                throw new \InvalidArgumentException("Setter requires a value");
            }
            $this->$property = $args[0];
            return $this; // 체이닝 가능하도록 자기 자신 반환
        }
    }
}