<?php

declare(strict_types=1);

namespace App\UniqueNameInterface;

class UserInterface
{
    public const FORM_LOGIN = 'login_form';
    public const FORM_LOGIN_USERNAME = 'username';
    public const FORM_LOGIN_PASSWORD = 'password';
    public const FORM_LOGIN_SUBMIT = 'submit';
    public const FORM_LOGIN_CSRF = 'csrf_token';
}