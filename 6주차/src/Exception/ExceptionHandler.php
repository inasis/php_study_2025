<?php
declare(strict_types=1);

namespace Fondue\Exception;

use Fondue\Exception\Http\NotFoundException;
use Fondue\Exception\Http\InternalServerErrorException;
use Fondue\Exception\Http\BadRequestException;
use Fondue\Exception\Http\UnauthorizedException;
use Fondue\Exception\Http\ForbiddenException;
use Fondue\Exception\Http\ValidationException;
use Fondue\Exception\Http\MethodNotAllowedException;

class ExceptionHandler
{
    private array $handlers = [];
    
    public function __construct()
    {
        $this->registerDefaultHandlers();
    }
    
    public function register(): void
    {
        set_exception_handler([$this, 'handle']);
    }
    
    private function registerDefaultHandlers(): void
    {
        // 400 Bad Request
        $this->handlers[BadRequestException::class] = function(\Throwable $e) {
            $this->sendJsonResponse(400, $e->getMessage());
        };
        
        // 401 Unauthorized
        $this->handlers[UnauthorizedException::class] = function(\Throwable $e) {
            $this->sendJsonResponse(401, $e->getMessage());
        };
        
        // 403 Forbidden
        $this->handlers[ForbiddenException::class] = function(\Throwable $e) {
            $this->sendJsonResponse(403, $e->getMessage());
        };
        
        // 404 Not Found
        $this->handlers[NotFoundException::class] = function(\Throwable $e) {
            $this->sendJsonResponse(404, $e->getMessage());
        };
        
        // 405 Method Not Allowed
        $this->handlers[MethodNotAllowedException::class] = function(\Throwable $e) {
            if ($e instanceof MethodNotAllowedException) {
                $allowedMethods = $e->getAllowedMethods();
                if (!empty($allowedMethods)) {
                    header("Allow: " . implode(', ', $allowedMethods));
                }
            }
            $this->sendJsonResponse(405, $e->getMessage());
        };
        
        // 422 Validation Error
        $this->handlers[ValidationException::class] = function(\Throwable $e) {
            $data = ['error' => 'Validation Failed'];
            
            if ($e instanceof ValidationException) {
                $errors = $e->getErrors();
                if (!empty($errors)) {
                    $data['errors'] = $errors;
                } else {
                    $data['message'] = $e->getMessage();
                }
            }
            
            $this->sendJsonResponse(422, 'Validation Failed', null, $data);
        };
        
        // 500 Internal Server Error
        $this->handlers[InternalServerErrorException::class] = function(\Throwable $e) {
            $this->logError($e);
            $this->sendJsonResponse(500, 'Internal Server Error', $e->getMessage());
        };
        
        // Base HTTP Exception (모든 HTTP 예외의 부모)
        $this->handlers[HttpException::class] = function(\Throwable $e) {
            $statusCode = $e->getCode() ?: 500;
            $message = $e->getMessage() ?: 'An error occurred';
            
            if ($statusCode >= 500) {
                $this->logError($e);
            }
            
            $this->sendJsonResponse($statusCode, http_response_code($statusCode) ?: 'Error', $message);
        };
        
        // Default handler for any other exception
        $this->handlers[\Exception::class] = function(\Throwable $e) {
            $this->logError($e);
            $this->sendJsonResponse(500, $e->getMessage());
        };
    }
    
    public function handle(\Throwable $e): void
    {
        $handler = $this->findHandler($e);
        $handler($e);
    }
    
    private function findHandler(\Throwable $e): callable
    {
        $class = get_class($e);
        
        // 정확히 일치하는 핸들러 찾기
        if (isset($this->handlers[$class])) {
            return $this->handlers[$class];
        }
        
        // 부모 클래스 핸들러 찾기
        foreach ($this->handlers as $exceptionClass => $handler) {
            if ($e instanceof $exceptionClass) {
                return $handler;
            }
        }
        
        // 기본 핸들러
        return $this->handlers[\Exception::class];
    }
    
    private function sendJsonResponse(
        int $statusCode,
        ?string $message = null,
        ?array $additionalData = null
    ): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        if ($message !== null && $message !== '') {
            $response['message'] = $message;
        }
         $response['code'] = $statusCode;
        
        if ($additionalData !== null) {
            $response = array_merge($response, $additionalData);
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    private function logError(\Throwable $e): void
    {
        error_log(sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
    }
    
    public function addHandler(string $exceptionClass, callable $handler): void
    {
        $this->handlers[$exceptionClass] = $handler;
    }
}
