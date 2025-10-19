<?php
declare(strict_types=1);

namespace Fondue\Exception\Http;

use Fondue\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct(
        string $message = "Not found",
        array $headers = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 404, $headers, $previous);
    }
}
