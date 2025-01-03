<?php

namespace App\Exception;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class HttpException extends \RuntimeException implements HttpExceptionInterface
{
    private int $statusCode;
    private array $headers;

    public function __construct(
        string $message         = '',
        int $statusCode         = 500,
        ?\Throwable $previous    = null,
        array $headers          = [],
        int $code               = 0,
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        // create default message as exception name (without exception itself)
        if (0 === strlen($message)) {
            $calledClass = get_called_class();
            $classname = substr($calledClass, strripos($calledClass, "\\") + 1);
            $message = ltrim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $classname));
        }

        $logger = new Logger('critical');
        $logger->pushHandler(new StreamHandler('../var/log/critical.log'));
        $logger->critical("Critical error happened!", [
            'msg'           => $message,
            'status'        => $statusCode,
            'stackTrace'    => [
                'file'              => $this->getFile(),
                'line'              => $this->getLine(),
                'fullStackTrace'    => $this->getTrace()
            ]
        ]);


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
