<?php

namespace App\Entity;

use App\UniqueNameInterface\AlertInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class Alert
{

    private string  $message;
    private int     $code;
    private string  $status;
    private string  $header;

    public function __construct (
        string  $message,
        int     $code,
        string  $status,
        string  $header,
        bool    $isToDelete
    )
    {
        $this->message = $message;
        $this->code = $code;
        $this->status = $status;
        $this->header = $header;
        // create session associated to alert only if it is not created via ajax call
        if (!$isToDelete) {
            $this->createSession();
        }
    }

    public static function success (
        string  $message,
        string  $header = 'Success',
        int     $code = 200,
        bool    $isToDelete = true
    ): Alert {
        return new Alert($message, $code, AlertInterface::SESSION_STATUS_SUCCESS, $header, $isToDelete);
    }

    public static function error (
        string  $message,
        string  $header = 'Oops! Something went wrong',
        int     $code = 500,
        bool    $isToDelete = true
    ): Alert {
        return new Alert($message, $code, AlertInterface::SESSION_STATUS_ERROR, $header, $isToDelete);
    }

    public static function warning (
        string  $message,
        string  $header = 'Information',
        int     $code = 200,
        bool    $isToDelete = true
    ) :Alert {
        return new Alert($message, $code, AlertInterface::SESSION_STATUS_WARNING, $header, $isToDelete);
    }

    public function getMessage (): string {
        return $this->message;
    }

    public function getCode (): int {
        return $this->code;
    }

    public function getHeader (): string {
        return $this->header;
    }

    public function getStatus (): string {
        return $this->status;
    }

    private function createSession (): Session {
        $session = new Session();
        $content = [
            AlertInterface::SESSION_MESSAGE => $this->getMessage(),
            AlertInterface::SESSION_STATUS  => $this->getStatus(),
            AlertInterface::SESSION_HEADER  => $this->getHeader(),
        ];
        $session->getFlashBag()->add(
            AlertInterface::SESSION_NAME,
            $content
        );

        return $session;
    }

}
