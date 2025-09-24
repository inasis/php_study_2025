<?php
declare(strict_types=1);

namespace Egg\Exception;

use Exception;

/**
 * 422 Unprocessable Entity
 */
class ValidationException extends Exception
{
    private array $errors = [];

    public function __construct(string $message = "유효성 검사에 실패했습니다.", array $errors = [], int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}
