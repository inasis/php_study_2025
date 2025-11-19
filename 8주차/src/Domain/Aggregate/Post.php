<?php
declare(strict_types=1);

namespace Hazelnut\Domain\Aggregate;

/**
 * 게시물의 상태와 변경 로직을 캡슐화합니다.
 * 
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $author
 * @property string $created_at
 * @property string $updated_at
 */
class Post
{
    public function __construct(
        private ?int $id,
        private string $title,
        private string $content,
        private int $author_id,
        private ?string $created_at,
        private ?string $updated_at
    ) {}

    public static function create(string $title, string $content, int $author_id): self
    {
        // 새 게시물 생성 시 ID와 시간은 아직 할당되지 않음
        return new self(
            id: null,
            title: $title,
            content: $content,
            author_id:  $author_id,
            created_at: null,
            updated_at: null
        );
    }
    
    // 게시물 정보 업데이트를 캡슐화합니다.
    public function update(?string $title, ?string $content): void
    {
        if ($title !== null) {
            $this->title = $title;
        }
        if ($content !== null) {
            $this->content = $content;
        }
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