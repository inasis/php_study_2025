<?php

namespace Ginger\Exception;

use Exception;

/**
 * 애플리케이션의 기본 예외 클래스
 * 모든 커스텀 예외는 이 클래스를 상속받습니다.
 */
class BaseException extends Exception
{
    protected array $context = [];

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * 컨텍스트 정보를 반환합니다
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * 컨텍스트 정보를 추가합니다
     */
    public function setContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }
}
