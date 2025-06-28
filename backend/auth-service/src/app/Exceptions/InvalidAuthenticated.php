<?php

use App\Exceptions\AuthException;

class  InvalidAuthenticated extends AuthException
{
    protected $message = 'Invalid authentication credentials provided.';
    protected $code = 401; // HTTP status code for Unauthorized
}
