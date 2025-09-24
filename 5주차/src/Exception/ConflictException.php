<?php
declare(strict_types=1);

namespace Egg\Exception;

use Exception;

/**
 * 409 Conflict
 */
class ConflictException extends Exception
{
    public function __construct(string $message = "요청이 현재 리소스 상태와 충돌합니다.", int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
