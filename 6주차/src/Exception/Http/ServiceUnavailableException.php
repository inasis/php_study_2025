<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Exception;

/**
 * 503 Service Unavailable
 */
class ServiceUnavailableException extends Exception
{
    public function __construct(string $message = "Service Unavailable", int $code = 503)
    {
        parent::__construct($message, $code);
    }
}
