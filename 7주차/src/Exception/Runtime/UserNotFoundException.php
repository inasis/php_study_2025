<?php

namespace Ginger\Exception\Runtime;

use Ginger\Exception\BaseException;

/**
 * User Not Found
 * 사용자를 찾을 수 없을 때 발생
 */
class UserNotFoundException extends BaseException
{
    public function __construct(
        string $message = "User not found",
        int $code = 0,
        ?\Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($message, $code, $previous, $context);
    }
}
