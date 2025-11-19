<?php
declare(strict_types=1);

namespace Hazelnut\Infrastructure\Routing\Exception;

use Exception;

class InternalServerErrorException extends Exception
{
    public function __construct(
        string $message = "Internal Server Error",
        int $code = 1000,
        ?\Throwable $previous = null,
        protected array $context = []
    ) {
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
