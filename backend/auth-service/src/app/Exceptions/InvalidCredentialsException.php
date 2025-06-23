<?php

// Ошибка: неверные логин/пароль

namespace App\Exceptions;

class InvalidCredentialsException extends AuthException
{
    protected $message = 'Invalid credentials provided.';
    protected $code = 401; // HTTP status code for Unauthorized
}
