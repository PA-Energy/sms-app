<?php

// Debug login endpoint
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;
use App\Models\User;
use App\Core\Auth;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        'error' => 'Invalid JSON',
        'raw_input' => file_get_contents('php://input'),
        'post_data' => $_POST
    ]);
    exit;
}

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$debug = [
    'username_received' => $username,
    'password_received' => !empty($password),
];

try {
    $db = Database::getInstance()->getConnection();
    $debug['database'] = 'connected';
    
    $user = User::findByUsername($username);
    $debug['user_found'] = $user ? true : false;
    
    if ($user) {
        $debug['user_id'] = $user['id'];
        $debug['password_hash'] = substr($user['password'], 0, 20) . '...';
        $debug['password_verify'] = password_verify($password, $user['password']);
        
        if (password_verify($password, $user['password'])) {
            $token = Auth::generateToken($user['id'], $user['username']);
            $debug['token_generated'] = $token ? true : false;
            
            if ($token) {
                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                    ],
                    'debug' => $debug
                ]);
                exit;
            }
        }
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Invalid credentials',
        'debug' => $debug
    ]);
    
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'debug' => $debug
    ]);
}
