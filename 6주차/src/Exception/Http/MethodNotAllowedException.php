<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Fondue\Exception\HttpException;

class MethodNotAllowedException extends HttpException
{
    private array $allowedMethods;
    
    public function __construct(
        array $allowedMethods = [],
        string $message = "Method not allowed",
        ?\Throwable $previous = null
    ) {
        $this->allowedMethods = $allowedMethods;
        
        $headers = [];
        if (!empty($allowedMethods)) {
            $headers['Allow'] = implode(', ', $allowedMethods);
        }
        
        parent::__construct($message, 405, $headers, $previous);
    }
    
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}