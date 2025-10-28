<?php

namespace Ginger\DTO\Validation;

interface ValidatorInterface
{
    /**
     * 속성 값의 유효성을 검사합니다.
     * @param string $propertyName 검사 중인 속성 이름
     * @param mixed $propertyValue 검사 중인 속성 값
     * @return string|null 유효성 검사 실패 시 에러 메시지 반환, 성공 시 null 반환
     */
    public function validate(string $propertyName, mixed $propertyValue): ?string;
}