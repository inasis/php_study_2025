<?php

namespace Fondue\Exception\Validation;

use Fondue\Exception\BaseException;

/**
 * Validation failed
 * 입력 데이터 검증 실패 시 발생
 */
class ValidationException extends BaseException
{
    protected array $errors = [];

    public function __construct(
        string $message = "Validation failed",
        array $errors = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous, ['errors' => $errors]);
    }
}
