<?php
declare(strict_types=1);

namespace Donut\Exception;

use Exception;

/**
 * 401 Unauthorized
 */
class UnauthorizedException extends Exception
{
    public function __construct(string $message = "인증이 필요합니다.", int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
