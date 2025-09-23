<?php
declare(strict_types=1);

namespace Donut\Exception;

use Exception;

/**
 * 404 Not Found
 */
class NotFoundException extends Exception
{
    public function __construct(string $message = "요청한 리소스를 찾을 수 없습니다.", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
