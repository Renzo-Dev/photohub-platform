<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Проверка токена и установка пользователя
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['message' => 'User not found'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Invalid or missing token'], 401);
        }

        return $next($request);
    }
}
