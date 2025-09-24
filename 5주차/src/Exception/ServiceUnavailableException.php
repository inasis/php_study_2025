<?php
declare(strict_types=1);

namespace Egg\Exception;

use Exception;

/**
 * 503 Service Unavailable
 */
class ServiceUnavailableException extends Exception
{
    public function __construct(string $message = "서비스를 일시적으로 이용할 수 없습니다.", int $code = 503)
    {
        parent::__construct($message, $code);
    }
}
