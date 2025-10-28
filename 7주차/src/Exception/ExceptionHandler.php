<?php

namespace Ginger\Exception;

use Ginger\Infrastructure\Logging\Logger;
use Throwable;

class ExceptionHandler
{
    private bool $debug;
    private array $dontReport = [];
    private array $statusCodeMap = [
        'NotFoundException' => 404,
        'UnauthorizedException' => 401,
        'ValidationException' => 422,
        'DatabaseException' => 500,
        'QueryException' => 500,
        'InfrastructureException' => 500,
        'RoutingException' => 500,
        'LoggingException' => 500,
    ];

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * 모든 예외를 처리하는 메인 핸들러
     */
    public function handle(Throwable $e): void
    {
        // 로깅
        $this->log($e);

        // HTTP 응답 전송
        $this->render($e);
    }

    /**
     * 예외를 Logger를 통해 기록
     */
    protected function log(Throwable $e): void
    {
        // 로깅하지 않을 예외 체크
        if ($this->shouldntReport($e)) {
            return;
        }

        try {
            $context = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
            ];

            // 디버그 모드일 때만 스택 트레이스 추가
            if ($this->debug) {
                $context['trace'] = $e->getTraceAsString();
            }

            // ValidationException의 경우 에러 상세 정보 추가
            if ($this->isInstanceOf($e, 'ValidationException') && method_exists($e, 'getErrors')) {
                $context['errors'] = $e->getErrors();
            }

            Logger::error($e->getMessage(), $context);
        } catch (Throwable $logError) {
            // 로깅 실패 시 PHP 기본 에러 로그로 대체
            error_log(sprintf(
                "Failed to log exception: %s (Original: %s)",
                $logError->getMessage(),
                $e->getMessage()
            ));
        }
    }

    /**
     * 예외를 HTTP 응답으로 렌더링
     */
    protected function render(Throwable $e): void
    {
        $statusCode = $this->getStatusCode($e);
        $response = $this->prepareResponse($e, $statusCode);

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * 예외로부터 HTTP 상태 코드 추출
     */
    protected function getStatusCode(Throwable $e): int
    {
        // HttpException 인터페이스나 메서드가 있는 경우
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        // 클래스명으로 상태 코드 매핑
        $className = $this->getShortClassName($e);
        
        return $this->statusCodeMap[$className] ?? 500;
    }

    /**
     * 응답 데이터 준비
     */
    protected function prepareResponse(Throwable $e, int $statusCode): array
    {
        $response = [
            'success' => false,
            'error' => [
                'message' => $e->getMessage() ?: 'An error occurred',
                'code' => $e->getCode(),
                'status' => $statusCode,
            ]
        ];

        // ValidationException의 경우 에러 상세 정보 추가
        if ($this->isInstanceOf($e, 'ValidationException') && method_exists($e, 'getErrors')) {
            $response['error']['errors'] = $e->getErrors();
        }

        // 디버그 모드일 때만 상세 정보 포함
        if ($this->debug) {
            $response['error']['exception'] = get_class($e);
            $response['error']['file'] = $e->getFile();
            $response['error']['line'] = $e->getLine();
            $response['error']['trace'] = explode("\n", $e->getTraceAsString());
        }

        return $response;
    }

    /**
     * 로깅하지 않을 예외인지 확인
     */
    protected function shouldntReport(Throwable $e): bool
    {
        foreach ($this->dontReport as $type) {
            if ($this->isInstanceOf($e, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 동적 클래스 인스턴스 체크
     */
    protected function isInstanceOf(Throwable $e, string $className): bool
    {
        $fullClassName = get_class($e);
        $shortClassName = $this->getShortClassName($e);

        // 짧은 클래스명 비교
        if ($shortClassName === $className) {
            return true;
        }

        // 전체 네임스페이스 비교
        if ($fullClassName === $className) {
            return true;
        }

        // 클래스가 존재하는 경우 instanceof 체크
        if (class_exists($className) || interface_exists($className)) {
            return $e instanceof $className;
        }

        return false;
    }

    /**
     * 짧은 클래스명 추출
     */
    protected function getShortClassName(Throwable $e): string
    {
        $className = get_class($e);
        $parts = explode('\\', $className);
        return end($parts);
    }

    /**
     * 로깅하지 않을 예외 클래스 설정
     */
    public function dontReport(array $exceptions): self
    {
        $this->dontReport = array_merge($this->dontReport, $exceptions);
        return $this;
    }

    /**
     * 상태 코드 매핑 추가
     */
    public function mapStatusCode(string $exceptionClass, int $statusCode): self
    {
        $this->statusCodeMap[$exceptionClass] = $statusCode;
        return $this;
    }

    /**
     * Fatal Error를 예외로 변환
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handle(new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    }

    /**
     * Error를 ErrorException으로 변환
     */
    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }

        return false;
    }

    /**
     * 글로벌 예외 핸들러로 등록
     */
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handle']);
        register_shutdown_function([$this, 'handleShutdown']);
    }
}