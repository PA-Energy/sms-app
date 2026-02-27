<?php

// Quick setup checker
require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Database;

echo "=== SMS App Setup Check ===\n\n";

// Check database connection
try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connection: OK\n";
} catch (\Exception $e) {
    echo "✗ Database connection: FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n";
    echo "\nMake sure:\n";
    echo "  1. MySQL container is running: docker-compose up -d mysql\n";
    echo "  2. Database credentials in .env are correct\n";
    exit(1);
}

// Check tables
$tables = ['users', 'user_tokens', 'sms_messages', 'sms_outbox', 'sms_batches', 'sms_batch_recipients'];
$db = Database::getInstance()->getConnection();

foreach ($tables as $table) {
    try {
        $stmt = $db->query("SHOW TABLES LIKE '{$table}'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '{$table}': EXISTS\n";
        } else {
            echo "✗ Table '{$table}': MISSING\n";
            echo "  Run: php setup-db.php\n";
        }
    } catch (\Exception $e) {
        echo "✗ Table '{$table}': ERROR - " . $e->getMessage() . "\n";
    }
}

// Check admin user
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✓ Admin user: EXISTS\n";
        echo "  Username: admin\n";
        echo "  Password: admin123\n";
    } else {
        echo "✗ Admin user: MISSING\n";
        echo "  Run: php setup-db.php\n";
    }
} catch (\Exception $e) {
    echo "✗ Admin user check: ERROR - " . $e->getMessage() . "\n";
}

echo "\n=== Check Complete ===\n";
