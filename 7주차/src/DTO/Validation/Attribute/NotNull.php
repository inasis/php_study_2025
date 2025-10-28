<?php
declare(strict_types=1);

namespace Ginger\DTO\Validation\Attribute;

use Ginger\DTO\Validation\ValidatorInterface;
use Attribute;

/**
 * 값이 null 또는 비어있는지 검사합니다.
 * 
 * @param string|null $message 유효성 검사 실패 시 사용할 메시지
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class NotNull implements ValidatorInterface
{
    public function __construct(
        private ?string $message = null
    ) {
        // 메시지가 설정되지 않은 경우 기본 메시지 설정
        $this->message = $this->message ?? '값은 비어있거나 null일 수 없습니다.';
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
        // null 또는 공백만 존재하거나 비어있는 문자열인 경우
        if ($propertyValue === null || (is_string($propertyValue) && trim($propertyValue) === '')) {
            return $this->message;
        }

        return null;
    }
}