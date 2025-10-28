<?php

namespace Ginger\Exception\Http;

/**
 * 405 Method Not Allowed
 * 요청된 리소스에 대해 허용되지 않은 HTTP 메서드를 사용했을 때 발생
 */
class MethodNotAllowedException extends HttpException
{
    public function __construct(
        string $message = "Method not allowed",
        int $code = 0,
        ?\Throwable $previous = null,
        array $headers = [],
        array $context = []
    ) {
        parent::__construct(405, $message, $code, $previous, $headers, $context);
    }
}