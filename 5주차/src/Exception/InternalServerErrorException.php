<?php
declare(strict_types=1);

namespace Egg\Exception;

use Exception;

/**
 * 500 Internal Server Error
 */
class InternalServerErrorException extends Exception
{
    public function __construct(string $message = "알 수 없는 오류가 발생했습니다.", int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
