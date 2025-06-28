<?php

namespace App\Services;

// работа с JWT: генерация, валидация, refresh

use App\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Str;

class JwtService
{
    // Set a new TTL for the refresh token from configuration
    public $ttl;

    public function __construct(CacheService $cacheService)
    {
        // Initialize the TTL for the refresh token from the configuration
        $this->ttl = config('auth.refresh_token_ttl');
    }

    public function generateAccessToken($credentials)
    {
        // Attempt to generate an access token using the provided credentials
        $token = auth()->attempt($credentials);

        // If the token is not generated, throw an exception
        if (!$token) {
            throw new InvalidCredentialsException();
        }

        // Return the generated access token
        return $token;
    }

    public function generateRefreshToken()
    {
        // Generate a new refresh token using UUID
        $refreshToken = Str::uuid()->toString();
        return $refreshToken;
    }

    public function refreshToken($refreshToken)
    {

    }

    // переделать выдачу refresh токена
    // инкапсулировать логику добавление удаление токенов в black list и удаление из кэша
    
}
