<?php

// Пользователь не найден

namespace App\Exceptions;

class UserNotFoundException extends AuthException
{
    protected $message = 'User not found';
    protected $code = 404;
}
