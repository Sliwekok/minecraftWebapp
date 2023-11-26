<?php

namespace App\Exception;

class HttpException extends \RuntimeException implements HttpExceptionInterface
{
    private int $statusCode;
    private array $headers;

    public function __construct(string $message = '', int $statusCode = 500, \Throwable $previous = null, array $headers = [], int $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
}