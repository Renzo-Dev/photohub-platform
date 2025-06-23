<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Registration
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $this->authService->register($data);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $creditials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        $response = $this->authService->login($creditials);
        return $this->getJsonResponse($response);
    }

    public function me()
    {
        try {
            $user = $this->authService->me();
            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve user: ' . $e->getMessage()], 400);
        }
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->cookie('refresh_token');
        $response = $this->authService->refresh($refreshToken);

        return $this->getJsonResponse($response);
    }

    public function logout(Request $request)
    {
        try {
            $refreshToken = $request->cookie('refresh_token'); // Get the refresh token from the request
            $accessToken = $request->cookie('access_token'); // Get the refresh token from the request
            $this->authService->logout($accessToken, $refreshToken); // Call the logout method in the AuthService

            return response()->json(['message' => 'User logged out successfully'], 200)->cookie('access_token', '', -1, '/')
                ->cookie('refresh_token', '', -1, '/');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Logout failed: ' . $e->getMessage()], 400);
        }
    }


    public function validate_token()
    {
        try {
            return response()->json(['message' => 'Token is valid'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token validation failed: ' . $e->getMessage()], 400);
        }
    }

    /**
     * @param array $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJsonResponse(array $response): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'user' => $response['user'],
            'expires_in' => $response['expires_in']
        ])->cookie('access_token',
            $response['access_token'],
            $response['expires_in'] / 60,   // В минутах
            '/',
            null,
            true,    // secure (только по https)
            true,    // httpOnly
            false,   // raw
            'Strict' // same-site)
        )->cookie('refresh_token',
            $response['refresh_token'],
            60 * 24 * 7,   // например, 7 дней
            '/',
            null,
            true,
            true,
            false,
            'Strict');
    }
}
