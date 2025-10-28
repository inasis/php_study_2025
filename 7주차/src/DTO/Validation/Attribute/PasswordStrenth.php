<?php
declare(strict_types=1);

namespace Ginger\DTO\Validation\Attribute;

use Ginger\DTO\Validation\ValidatorInterface; // ValidatorInterface 사용
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PasswordStrength implements ValidatorInterface
{
    private const DEFAULT_MIN_LENGTH = 8;

    /**
     * @param int $minLength 최소 길이
     * @param string|null $message 전체 실패 시 사용할 기본 메시지
     * @param string|null $minLenMessage 최소 길이 실패 시 메시지
     * @param string|null $upperCaseMessage 대문자 포함 실패 시 메시지
     * @param string|null $lowerCaseMessage 소문자 포함 실패 시 메시지
     * @param string|null $digitMessage 숫자 포함 실패 시 메시지
     */
    public function __construct(
        private int $minLength = self::DEFAULT_MIN_LENGTH,
        private ?string $message = null,
        private ?string $minLenMessage = null,
        private ?string $upperCaseMessage = null,
        private ?string $lowerCaseMessage = null,
        private ?string $digitMessage = null
    ) {
        // 기본 메시지 설정 (커스터마이징 되지 않았을 경우)
        $this->minLenMessage = $this->minLenMessage ?? "must be at least {$this->minLength} characters long";
        $this->upperCaseMessage = $this->upperCaseMessage ?? "must include at least one uppercase letter";
        $this->lowerCaseMessage = $this->lowerCaseMessage ?? "must include at least one lowercase letter";
        $this->digitMessage = $this->digitMessage ?? "must include at least one digit";
    }

    /**
     * 비밀번호 강도를 검사합니다.
     * DtoValidator의 ValidatorInterface::validate() 요구사항에 따라 에러 메시지 또는 null을 반환합니다.
     *
     * @param string $propertyName 검사 중인 속성 이름 (사용하지 않지만 인터페이스 준수를 위해 유지)
     * @param mixed $propertyValue 검사 중인 속성 값
     * @return string|null 유효성 검사 실패 시 통합 에러 메시지 반환, 성공 시 null 반환
     */
    public function validate(string $propertyName, mixed $propertyValue): ?string
    {
        // NotNull 검증은 별도 Validator로 존재하고, 문자열이 아닌 경우는 
        // 다른 Validator가 처리하도록 MinLength 등 문자열 전용 검사를 스킵하고 null 반환
        if (!is_string($propertyValue) || trim($propertyValue) === '') {
            return null; 
        }
        
        $errors = [];

        // 최소 길이 검증
        if (strlen($propertyValue) < $this->minLength) {
            $errors[] = $this->minLenMessage;
        }

        // 대문자 포함 검증
        if (!preg_match('/[A-Z]/', $propertyValue)) {
            $errors[] = $this->upperCaseMessage;
        }

        // 소문자 포함 검증
        if (!preg_match('/[a-z]/', $propertyValue)) {
            $errors[] = $this->lowerCaseMessage;
        }

        // 숫자 포함 검증
        if (!preg_match('/\d/', $propertyValue)) {
            $errors[] = $this->digitMessage;
        }

        if (!empty($errors)) {
            // 커스터마이징된 전체 메시지가 있으면 그것을 사용
            if ($this->message !== null) {
                return $this->message;
            }
            
            // 그렇지 않으면 개별 오류 메시지들을 통합하여 반환
            return implode(', ', $errors);
        }
        
        return null;
    }
}