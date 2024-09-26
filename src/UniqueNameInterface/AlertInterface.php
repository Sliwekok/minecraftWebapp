<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class AlertInterface
{
    public const SESSION_NAME = 'alert';
    public const SESSION_STATUS = 'status';
    public const SESSION_HEADER = 'header';
    public const SESSION_MESSAGE = 'message';
    public const SESSION_STATUS_SUCCESS = 'success';
    public const SESSION_STATUS_WARNING = 'warning';
    public const SESSION_STATUS_ERROR = 'error';
}
