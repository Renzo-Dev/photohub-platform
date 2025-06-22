<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
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

            $token = $this->authService->login($creditials);

            return response()->json([
                'access_token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => UserDTO::fromModel(auth()->user())->toArray(),
            ], 200);

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

    public function refresh()
    {
        try {
            $token = auth()->refresh();
            return response()->json(['token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token refresh failed: ' . $e->getMessage()], 400);
        }
    }

    public function logout()
    {
    }

    public function changePassword()
    {
    }
}
