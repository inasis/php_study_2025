<?php

namespace Ginger\Exception\Http;

use Ginger\Exception\BaseException;

class HttpException extends BaseException
{
    public function __construct(
        protected int $statusCode,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null,
        protected array $headers = [],
        array $context = []
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous, $context);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
}
