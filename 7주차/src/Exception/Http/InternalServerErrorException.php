<?php

namespace Ginger\Exception\Http;

/**
 * 500 Internal Server Error
 * 서버 내부 오류 발생 시 사용
 */
class InternalServerErrorException extends HttpException
{
    public function __construct(
        string $message = "Internal server error",
        int $code = 0,
        ?\Throwable $previous = null,
        array $headers = [],
        array $context = []
    ) {
        parent::__construct(500, $message, $code, $previous, $headers, $context);
    }
}
