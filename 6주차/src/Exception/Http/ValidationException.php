<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Fondue\Exception\HttpException;

class ValidationException extends HttpException
{
    private array $errors;
    
    public function __construct(
        array $errors = [],
        string $message = "Validation failed",
        ?\Throwable $previous = null
    ) {
        $this->errors = $errors;
        parent::__construct($message, 422, [], $previous);
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
}