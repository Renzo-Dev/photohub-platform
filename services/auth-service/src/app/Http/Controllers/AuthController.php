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
                'message' => 'Login successful',
                'token' => $token,
                'user' => auth()->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Login failed: ' . $e->getMessage()], 401);
        }
    }

    public function me()
    {
        try {
            dd('WORK');
            $user = $this->authService->me();
            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve user: ' . $e->getMessage()], 400);
        }
    }

    public function refresh()
    {
    }

    public function logout()
    {
    }

    public function changePassword()
    {
    }
}
