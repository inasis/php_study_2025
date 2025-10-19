<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Fondue\Exception\HttpException;

/**
 * 400 Bad Request
 */
class BadRequestException extends \Exception
{
    public function __construct(
        string $message = "Bad request",
        array $headers = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 400, $headers, $previous);
    }
}
