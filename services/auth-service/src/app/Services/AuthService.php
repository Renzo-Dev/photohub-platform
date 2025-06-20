<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register($data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function login($credentials)
    {
        $token = auth()->attempt($credentials);
        if (!$token) {
            throw new \Exception('Invalid credentials');
        }
        return $token;
    }

    public function me()
    {
        return Auth::user();
    }

    public function refresh()
    {
        // Refresh the authentication token
    }

    public function logout()
    {
        // Invalidate the authentication token
    }
}
