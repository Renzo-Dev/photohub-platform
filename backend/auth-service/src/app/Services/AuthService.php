<?php

// логика логина, регистрации, логаута, восстановления пароля

namespace App\Services;

use App\DTO\UserDTO;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserNotFoundException;
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
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        return $user;
    }

    public function login($credentials)
    {
        $token = auth()->attempt($credentials);
        if (!$token) {
            throw new InvalidCredentialsException();
        }

        // If the user is authenticated, get the user instance
        $user = auth()->user();
        // Generate a new access token
        $refreshToken = Str::uuid()->toString();

        // Store the refresh token in the cache with the user ID
//        Cache::put("refresh_token:{$refreshToken}", $user->id, $this->ttl);
        Cache::put("refresh_token:{$refreshToken}", [
            'user_id' => $user->id,
            'access_token' => $token,
        ], $this->ttl);

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
        if (!$data = Cache::get("refresh_token:{$refreshToken}")) {
            throw new \Exception('Invalid refresh token');
        }

        // Find the user by ID
        $user = User::find($data['user_id']);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // Generate a new access token
        $newAccessToken = JWTAuth::fromUser($user);

        // Remove the old refresh token from the cache
        Cache::forget("refresh_token:{$refreshToken}");

        // Generate a new refresh token
        $newRefreshToken = Str::uuid()->toString();

        // Store the new refresh token in the cache
        Cache::put("refresh_token:{$newRefreshToken}", [
            'user_id' => $user->id,
            'access_token' => $newAccessToken,
        ], $this->ttl);

        // Invalidate the old access token
//        $this->logout($data['access_token'], null);

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
