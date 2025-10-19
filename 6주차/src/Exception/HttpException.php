<?php
declare(strict_types=1);

namespace Fondue\Exception;

abstract class HttpException extends \Exception
{
    protected int $statusCode;
    protected array $headers = [];
    
    public function __construct(
        string $message = "",
        int $statusCode = 500,
        array $headers = [],
        ?\Throwable $previous = null
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        
        parent::__construct($message, $statusCode, $previous);
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
}