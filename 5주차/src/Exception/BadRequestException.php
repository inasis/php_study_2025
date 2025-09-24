<?php
declare(strict_types=1);

namespace Egg\Exception;

use Exception;

/**
 * 400 Bad Request
 */
class BadRequestException extends Exception
{
    public function __construct(string $message = "잘못된 요청입니다.", int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
