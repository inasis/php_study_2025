<?php
declare(strict_types=1);

namespace Egg\Exception;

use Exception;

/**
 * 403 Forbidden
 */
class ForbiddenException extends Exception
{
    public function __construct(string $message = "접근 권한이 없습니다.", int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
