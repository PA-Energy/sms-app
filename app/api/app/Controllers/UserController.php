<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        Auth::requireAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
        $search = $_GET['search'] ?? '';

        $result = User::getAll($page, $perPage, $search);
        
        return $this->json([
            'success' => true,
            'data' => $result['data'],
            'pagination' => [
                'current_page' => $result['current_page'],
                'per_page' => $result['per_page'],
                'total' => $result['total'],
                'last_page' => $result['last_page'],
            ]
        ]);
    }

    public function store()
    {
        Auth::requireAdmin();
        
        $data = $this->getRequestData();
        $errors = $this->validate($data, [
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors], 422);
        }

        // Check if username already exists
        if (User::findByUsername($data['username'])) {
            return $this->json(['success' => false, 'message' => 'Username already exists'], 422);
        }

        $role = $data['role'] ?? 'user';
        if (!in_array($role, ['admin', 'user'])) {
            $role = 'user';
        }

        $userId = User::create($data['username'], $data['email'], $data['password'], $role);
        
        $user = User::findById($userId);
        unset($user['password']);

        return $this->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    public function update($id)
    {
        Auth::requireAdmin();
        
        $data = $this->getRequestData();
        
        $user = User::findById($id);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Prevent editing admin user's role (optional security measure)
        if ($user['role'] === 'admin' && isset($data['role']) && $data['role'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Cannot change admin role'], 403);
        }

        $username = $data['username'] ?? null;
        $email = $data['email'] ?? null;
        $role = $data['role'] ?? null;
        $password = $data['password'] ?? null;

        // Check if username is being changed and already exists
        if ($username && $username !== $user['username'] && User::findByUsername($username)) {
            return $this->json(['success' => false, 'message' => 'Username already exists'], 422);
        }

        User::update($id, $username, $email, $role, $password);
        
        $updatedUser = User::findById($id);
        unset($updatedUser['password']);

        return $this->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $updatedUser
        ]);
    }

    public function destroy($id)
    {
        Auth::requireAdmin();
        
        $user = User::findById($id);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Prevent deleting admin users
        if ($user['role'] === 'admin') {
            return $this->json(['success' => false, 'message' => 'Cannot delete admin user'], 403);
        }

        if (User::delete($id)) {
            return $this->json(['success' => true, 'message' => 'User deleted successfully']);
        }

        return $this->json(['success' => false, 'message' => 'Failed to delete user'], 500);
    }

    public function show($id)
    {
        Auth::requireAdmin();
        
        $user = User::findById($id);
        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User not found'], 404);
        }

        unset($user['password']);
        return $this->json(['success' => true, 'data' => $user]);
    }
}
