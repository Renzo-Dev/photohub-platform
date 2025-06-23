<?php

// логика логина, регистрации, логаута, восстановления пароля

namespace App\Services;

use App\DTO\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    // Set a new TTL for the refresh token from configuration
    protected $ttl;

    public function __construct()
    {
        $this->ttl = config('auth.refresh_token_ttl');
    }

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

        // If the user is authenticated, get the user instance
        $user = auth()->user();
        // Generate a new access token
        $refreshToken = Str::uuid()->toString();

        // Store the refresh token in the cache with the user ID
        Cache::put("refresh_token:{$refreshToken}", $user->id, $this->ttl);

        return [
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => UserDTO::fromModel($user)->toArray(),
        ];
    }

    public function me()
    {
        return Auth::user();
    }

    public function refresh($refreshToken)
    {
        // Get the user ID from the cache using the refresh token
        if (!$userId = Cache::get("refresh_token:{$refreshToken}")) {
            throw new \Exception('Invalid refresh token');
        }
        // Find the user by ID
        $user = User::find($userId);
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Generate a new access token
        $newAccessToken = JWTAuth::fromUser($user);
        Cache::forget("refresh_token:{$refreshToken}");
        $newRefreshToken = Str::uuid()->toString();
        // Store the new refresh token in the cache
        Cache::put("refresh_token:{$newRefreshToken}", $user->id, $this->ttl);
        // Invalidate the old access token
        $this->logout(JWTAuth::getToken(),null);

        return [
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => UserDTO::fromModel($user)->toArray(),
        ];
    }

    public function logout(string $accessToken, ?string $refreshToken)
    {
        try {
            // Invalidate the access token
            JWTAuth::setToken($accessToken)->invalidate();
        } catch (\Throwable $e) {
        }
        // if the refresh token is provided, remove it from the cache
        if ($refreshToken) {
            Cache::forget("refresh_token:{$refreshToken}");
        }

    }
}
