<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Fondue\Exception\HttpException;

class ForbiddenException extends HttpException
{
    public function __construct(
        string $message = "Forbidden",
        array $headers = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 403, $headers, $previous);
    }
}