<?php

namespace App\Exception;

interface HttpExceptionInterface extends \Throwable
{
    public function getStatusCode(): int;

    public function getHeaders(): array;
}