<?php

// логика логина, регистрации, логаута, восстановления пароля

namespace App\Services;

use App\DTO\UserDTO;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected JwtService $jwtService;
    protected UserService $userService;
    protected CacheService $cacheService;

    public function __construct(JwtService $jwtService, UserService $userService, CacheService $cacheService)
    {
        $this->jwtService = $jwtService;
        $this->userService = $userService;
        $this->cacheService = $cacheService;
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
        // Validate the credentials and attempt to generate an access token
        $token = $this->jwtService->generateAccessToken($credentials);

        // If the user is authenticated, get the user instance
        $user = $this->userService->getUser();

        // Generate a new access token
        $refreshToken = $this->jwtService->generateRefreshToken();

        $this->cacheService->storeRefreshTokenInCache($refreshToken, $user->id, $token, $this->jwtService->ttl);

        // Return the access token, refresh token, and user data
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
