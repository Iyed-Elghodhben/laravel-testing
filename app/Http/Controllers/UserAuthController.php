<?php

namespace App\Http\Controllers;

use App\Services\UserAuthService;
use App\Http\Requests\StoreUserAuthRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserAuthController extends Controller
{
    private UserAuthService $authService;

    public function __construct(UserAuthService $authService)
    {
        $this->authService = $authService;
    }

        public function register(StoreUserAuthRequest $request): JsonResponse
        {
            $user = $this->authService->register($request->validated());

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ], 201);
        }




    public function login(LoginUserRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->email, $request->password);
        if (!$result) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => $result['user'],
            'token' => $result['token'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['message' => 'Logged out successfully']);
    }
}
