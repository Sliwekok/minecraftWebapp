<?php

namespace App\Entity;

class Alert
{

    private string  $message;
    private int     $code;

    public function __construct (
        string  $message,
        int     $code
    )
    {
        $this->message = $message;
        $this->code = $code;
    }

    public static function success (
        string  $message,
        int     $code = 200
    ): Alert {
        return new Alert($message, $code);
    }

    public static function error (
        string  $message,
        int     $code = 500
    ): Alert {
        return new Alert($message, $code);
    }

    public function getMessage (): string {
        return $this->message;
    }

    public function getCode (): int {
        return $this->code;
    }

}
