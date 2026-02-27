<?php

// Script to fix admin user role
// Run this if your admin user doesn't have admin access

require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if role column exists
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($stmt->rowCount() === 0) {
        echo "Adding role column to users table...\n";
        $db->exec("ALTER TABLE users ADD COLUMN role ENUM('admin', 'user') DEFAULT 'user' AFTER password");
        echo "Role column added!\n";
    }
    
    // Update admin user to have admin role
    $stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE username = 'admin'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Admin user role updated to 'admin'\n";
    } else {
        // Check if admin user exists
        $checkStmt = $db->prepare("SELECT * FROM users WHERE username = 'admin'");
        $checkStmt->execute();
        $admin = $checkStmt->fetch();
        
        if ($admin) {
            echo "✓ Admin user already has 'admin' role\n";
        } else {
            echo "✗ Admin user not found. Creating admin user...\n";
            $createStmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
            $createStmt->execute(['admin', 'admin@smsapp.com', password_hash('admin123', PASSWORD_DEFAULT)]);
            echo "✓ Admin user created with 'admin' role\n";
        }
    }
    
    // Verify
    $verifyStmt = $db->prepare("SELECT username, role FROM users WHERE username = 'admin'");
    $verifyStmt->execute();
    $admin = $verifyStmt->fetch();
    
    if ($admin) {
        echo "\n=== Admin User Status ===\n";
        echo "Username: {$admin['username']}\n";
        echo "Role: {$admin['role']}\n";
        echo "\n";
        
        if ($admin['role'] === 'admin') {
            echo "✓ Admin user has correct role!\n";
            echo "\nNote: You may need to logout and login again for the role to take effect.\n";
        } else {
            echo "✗ Admin user role is: {$admin['role']} (should be 'admin')\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
