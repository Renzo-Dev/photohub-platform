<?php

// проблемы со сбросом пароля

namespace App\Exceptions;

class PasswordResetException extends AuthException
{
    protected $message = 'There was an issue with the password reset process.';
    protected  $code = 403;
}
