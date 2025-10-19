<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Fondue\Exception\HttpException;

/**
 * 401 Unauthorized
 */
class UnauthorizedException extends HttpException
{
    public function __construct(
        string $message = "Unauthorized",
        array $headers = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 401, $headers, $previous);
    }
}
