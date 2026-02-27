<?php

// Test database configuration loading
require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

echo "=== Database Configuration Test ===\n\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_PORT: " . DB_PORT . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_PASS: " . (DB_PASS ? '***' : '(empty)') . "\n\n";

// Test connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✓ Database connection: SUCCESS\n";
} catch (PDOException $e) {
    echo "✗ Database connection: FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n";
    echo "\nPlease check your configuration in:\n";
    echo "  1. app/api/.env file (highest priority)\n";
    echo "  2. app/api/config/database.php (fallback)\n";
}
