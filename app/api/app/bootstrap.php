<?php

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Load database config from config/database.php if it exists
$dbConfig = null;
if (file_exists(__DIR__ . '/../config/database.php')) {
    $dbConfigArray = require __DIR__ . '/../config/database.php';
    if (isset($dbConfigArray['connections']['mysql'])) {
        $dbConfig = $dbConfigArray['connections']['mysql'];
    }
}

// Database configuration - priority: .env > config/database.php > defaults
// The config file's env() calls will use $_ENV values we loaded above
define('DB_HOST', $_ENV['DB_HOST'] ?? ($dbConfig['host'] ?? '127.0.0.1'));
define('DB_PORT', $_ENV['DB_PORT'] ?? ($dbConfig['port'] ?? '3306'));
define('DB_NAME', $_ENV['DB_DATABASE'] ?? ($dbConfig['database'] ?? 'sms_app'));
define('DB_USER', $_ENV['DB_USERNAME'] ?? ($dbConfig['username'] ?? 'root'));
define('DB_PASS', $_ENV['DB_PASSWORD'] ?? ($dbConfig['password'] ?? ''));

// Session token expiry (hours)
define('TOKEN_EXPIRY_HOURS', $_ENV['TOKEN_EXPIRY_HOURS'] ?? 24);

// GoIP Configuration
define('GOIP_ADDR', $_ENV['GOIP_ADDR'] ?? '192.168.1.3');
define('GOIP_USER', $_ENV['GOIP_USER'] ?? 'admin');
define('GOIP_PASSWORD', $_ENV['GOIP_PASSWORD'] ?? 'admin');
define('GOIP_LINE', $_ENV['GOIP_LINE'] ?? 1);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
