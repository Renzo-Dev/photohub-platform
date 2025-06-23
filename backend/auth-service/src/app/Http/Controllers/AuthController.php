<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $this->authService->register($data);

            return response()->json(['message' => 'User registered successfully'], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $creditials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string|min:6',
            ]);

            $response = $this->authService->login($creditials);
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Login failed: ' . $e->getMessage()], 401);
        }
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
        try {

            // Get the refresh token from the request
            $refreshToken = $request->input('refresh_token');
            
            $response = $this->authService->refresh($refreshToken);

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token refresh failed: ' . $e->getMessage()], 400);
        }
    }

    public function logout()
    {
        try {
            $accessToken = JWTAuth::getToken(); // Get the current access token
            $refreshToken = request()->input('refresh_token'); // Get the refresh token from the request

            $this->authService->logout($accessToken, $refreshToken); // Call the logout method in the AuthService

            return response()->json(['message' => 'User logged out successfully'], 200);
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
}
