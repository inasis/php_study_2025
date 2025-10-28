<?php

namespace Ginger\Exception\Runtime;

use Ginger\Exception\BaseException;

/**
 * Authentication Error
 * 계정을 인증하지 못했을 때 발생
 */
class AuthenticationException extends BaseException
{
    public function __construct(
        string $message = "Authentication Error",
        int $code = 0,
        ?\Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}
