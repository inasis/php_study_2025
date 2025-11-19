<?php
declare(strict_types=1);

namespace Hazelnut\Application\DTO\Validation\Constraints;

use Hazelnut\Application\DTO\Validation\ValidatorInterface;
use Attribute;

/**
 * 최소 길이 검사를 수행합니다.
 * 
 * @param int $min 최소 허용 길이
 * @param string|null $message 유효성 검사 실패 시 사용할 메시지
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MinLength implements ValidatorInterface
{
    
    public function __construct(
        private int $min,
        private ?string $message = null
    ) {
        // 메시지가 설정되지 않은 경우 기본 메시지 생성
        $this->message = $this->message ?? "값은 최소 {$this->min}자 이상이어야 합니다.";
    }

    /**
     * 속성 값의 유효성을 검사합니다.
     * 
     * @param string $propertyName 검사 중인 속성 이름
     * @param mixed $propertyValue 검사 중인 속성 값
     * @return string|null 실패 시 오류 메시지, 성공 시 null 반환
     */
    public function validate(string $propertyName, mixed $propertyValue): ?string
    {
        // null 또는 공백만 존재하거나 비어있는 문자열인 경우, 길이 검사는 건너뜁니다.
        if ($propertyValue === null || (is_string($propertyValue) && trim($propertyValue) === '')) {
            return null;
        }

        // 실제 길이 검사는 멀티바이트 문자열 지원을 위해 mb_strlen 사용을 권장합니다.
        if (mb_strlen($propertyValue) < $this->min) {
            return $this->message;
        }

        return null;
    }
}