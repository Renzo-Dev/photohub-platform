<?php

namespace App\Services;

use InvalidAuthenticated;

class UserService
{
    public function __construct()
    {

    }

    public function getUser()
    {
        // If the user is authenticated, get the user instance
        $user = auth()->user();

        if (!$user) {
            throw new InvalidAuthenticated();
        }

        return $user;
    }
}
