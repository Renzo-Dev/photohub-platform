<?php

// Ошибка: токен истек

namespace App\Exceptions;

class TokenExpiredException extends AuthException
{
    protected $message = 'The provided token has expired.';
    protected  $code = 401;
}
