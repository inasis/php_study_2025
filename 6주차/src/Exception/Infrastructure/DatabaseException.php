<?php
declare(strict_types=1);

namespace Fondue\Exception\Infrastructure;

use Throwable;

/**
 * 데이터베이스 작업 중 발생한 예상치 못한 내부 오류를 나타냅니다.
 * 이 예외는 Service 계층을 통과하여 최종적으로 HTTP 500으로 처리되어야 합니다.
 */
class DatabaseException extends \RuntimeException
{
    /**
     * @param string $message 예외 메시지
     * @param int $code 예외 코드 (기본값 0)
     * @param Throwable|null $previous 이전 예외 (원래의 PDOException 등)
     */
    public function __construct(string $message = "데이터베이스 내부 오류가 발생했습니다.", int $code = 0, ?Throwable $previous = null)
    {
        // Infrastructure 관련 예외는 항상 500 상태 코드와 관련된 문제를 나타냅니다.
        // 하지만 이 클래스는 HTTP 계층이 아니므로 상태 코드를 명시적으로 지정하지 않고
        // RuntimeException의 기본 코드를 사용하거나 0을 사용합니다.
        parent::__construct($message, $code, $previous);
    }
}