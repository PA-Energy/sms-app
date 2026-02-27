<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('username', 'password');

        try {
            $user = User::where('username', $credentials['username'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not create token',
                ], 500);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not create token',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate();
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout',
            ], 500);
        }
    }
}
