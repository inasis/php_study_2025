<?php

namespace Ginger\Exception\Http;

/**
 * 404 Not Found
 * 리소스를 찾을 수 없을 때 발생
 */
class NotFoundException extends HttpException
{
    public function __construct(
        string $message = "Not found",
        int $code = 0,
        ?\Throwable $previous = null,
        array $headers = [],
        array $context = []
    ) {
        parent::__construct(404, $message, $code, $previous, $headers, $context);
    }
}
