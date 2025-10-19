<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Fondue\Exception\HttpException;

class InternalServerErrorException extends HttpException
{
    public function __construct(
        string $message = "Internal server error",
        array $headers = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 500, $headers, $previous);
    }
}