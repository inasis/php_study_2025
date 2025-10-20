<?php

namespace Fondue\Exception\Http;

/**
 * 401 Unauthorized
 * 인증이 필요하거나 인증에 실패했을 때 발생
 */
class UnauthorizedException extends HttpException
{
    public function __construct(
        string $message = "Unauthorized",
        int $code = 0,
        ?\Throwable $previous = null,
        array $headers = [],
        array $context = []
    ) {
        parent::__construct(401, $message, $code, $previous, $headers, $context);
    }
}
