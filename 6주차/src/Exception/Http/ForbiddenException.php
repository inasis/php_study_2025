<?php

namespace Fondue\Exception\Http;

/**
 * 403 Forbidden
 * 리소스에 대한 접근 권한이 없을 때 발생
 */
class ForbiddenException extends HttpException
{
    public function __construct(
        string $message = "Forbidden",
        int $code = 0,
        ?\Throwable $previous = null,
        array $headers = [],
        array $context = []
    ) {
        parent::__construct(403, $message, $code, $previous, $headers, $context);
    }
}