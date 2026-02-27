<?php

// Simple test script to debug login
require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;
use App\Models\User;

echo "Testing Login...\n\n";

// Test database connection
try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connection: OK\n";
} catch (\Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if user_tokens table exists
try {
    $stmt = $db->query("SHOW TABLES LIKE 'user_tokens'");
    if ($stmt->rowCount() > 0) {
        echo "✓ user_tokens table: EXISTS\n";
    } else {
        echo "✗ user_tokens table: MISSING - Run setup-db.php\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking tables: " . $e->getMessage() . "\n";
}

// Check if admin user exists
$user = User::findByUsername('admin');
if ($user) {
    echo "✓ Admin user: EXISTS\n";
    echo "  User ID: " . $user['id'] . "\n";
    echo "  Username: " . $user['username'] . "\n";
    
    // Test password verification
    $testPassword = 'admin123';
    if (password_verify($testPassword, $user['password'])) {
        echo "✓ Password verification: OK\n";
    } else {
        echo "✗ Password verification: FAILED\n";
        echo "  Note: Password hash might be incorrect. Try running setup-db.php again.\n";
    }
} else {
    echo "✗ Admin user: NOT FOUND - Run setup-db.php\n";
}

// Test token generation
if ($user) {
    try {
        $token = \App\Core\Auth::generateToken($user['id'], $user['username']);
        if ($token) {
            echo "✓ Token generation: OK\n";
            echo "  Token: " . substr($token, 0, 20) . "...\n";
        } else {
            echo "✗ Token generation: FAILED\n";
        }
    } catch (\Exception $e) {
        echo "✗ Token generation error: " . $e->getMessage() . "\n";
    }
}

echo "\nTest complete!\n";
