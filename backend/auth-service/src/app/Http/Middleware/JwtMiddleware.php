<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mockery\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->cookie('access_token');
        if (!$accessToken) {
            throw new Exception('Access token not found');
        }

        // Проверка токена и установка пользователя
        if (!$user = JWTAuth::setToken($accessToken)->authenticate()) {
            return response()->json(['message' => 'User not found'], 401);
        }

        return $next($request);
    }
}
