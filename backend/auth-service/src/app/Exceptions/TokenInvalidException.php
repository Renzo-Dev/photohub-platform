<?php

// токен недействителен

namespace App\Exceptions;

class TokenInvalidException extends AuthException
{
    protected $message = 'The provided token is invalid.';
    protected  $code = 401;
}
