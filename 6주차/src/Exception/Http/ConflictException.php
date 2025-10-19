<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Exception;

/**
 * 409 Conflict
 */
class ConflictException extends Exception
{
    public function __construct(string $message = "Conflict", int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
