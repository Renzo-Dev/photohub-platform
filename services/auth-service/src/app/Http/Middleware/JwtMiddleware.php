<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Попытка авторизовать пользователя по токену
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return response()->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
            }

        } catch (JWTException $e) {
            return response()->json(['message' => 'Token error: ' . $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
