<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        try {
            $data = $this->getRequestData();
            $errors = $this->validate($data, [
                'username' => 'required',
                'password' => 'required',
            ]);

            if (!empty($errors)) {
                return $this->json(['success' => false, 'errors' => $errors], 422);
            }

            $user = User::findByUsername($data['username']);
            
            if (!$user) {
                return $this->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            if (!password_verify($data['password'], $user['password'])) {
                return $this->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }

            $token = Auth::generateToken($user['id'], $user['username']);

            if (!$token) {
                error_log("AuthController: Token generation returned null for user_id: " . $user['id'] . " | Username: " . $user['username']);
                // Try to get more info about the error
                $db = Database::getInstance()->getConnection();
                $errorInfo = $db->errorInfo();
                if ($errorInfo[0] !== '00000') {
                    error_log("Database error info: " . json_encode($errorInfo));
                }
                return $this->json([
                    'success' => false, 
                    'message' => 'Failed to generate token. Please check server logs.'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'] ?? 'user',
                ],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function me()
    {
        $user = Auth::requireAuth();
        return $this->json(['success' => true, 'user' => $user]);
    }

    public function logout()
    {
        $user = Auth::getCurrentUser();
        if ($user) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                Auth::revokeToken($matches[1]);
            }
        }
        return $this->json(['success' => true, 'message' => 'Successfully logged out']);
    }
}
