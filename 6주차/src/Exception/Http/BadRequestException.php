<?php

namespace Fondue\Exception\Http;

/**
 * 400 Bad Request
 * 유효하지 않은 입력으로 클라이언트의 요청이 잘못되었을 때 발생
 */
class BadRequestException extends HttpException
{
    public function __construct(
        string $message = "Bad request",
        int $code = 0,
        ?\Throwable $previous = null,
        array $headers = [],
        array $context = []
    ) {
        parent::__construct(400, $message, $code, $previous, $headers, $context);
    }
}