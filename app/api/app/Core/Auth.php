<?php

namespace App\Core;

use App\Core\Database;

class Auth
{
    public static function generateToken($userId, $username)
    {
        try {
            // Validate inputs
            if (empty($userId) || !is_numeric($userId)) {
                error_log("Token generation failed: Invalid user_id: " . var_export($userId, true));
                return null;
            }
            
            // Simple token: random hex string (64 characters)
            $token = bin2hex(random_bytes(32));
            $expiryHours = defined('TOKEN_EXPIRY_HOURS') ? TOKEN_EXPIRY_HOURS : 24;
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours"));
            
            $db = Database::getInstance()->getConnection();
            
            // Check if user exists
            $userCheck = $db->prepare("SELECT id FROM users WHERE id = ?");
            $userCheck->execute([$userId]);
            if (!$userCheck->fetch()) {
                error_log("Token generation failed: User ID $userId does not exist");
                return null;
            }
            
            // Insert token with retry logic for duplicate token (very unlikely but possible)
            $maxRetries = 3;
            $retryCount = 0;
            
            while ($retryCount < $maxRetries) {
                try {
                    $stmt = $db->prepare("INSERT INTO user_tokens (user_id, token, expires_at, created_at) VALUES (?, ?, ?, NOW())");
                    $result = $stmt->execute([$userId, $token, $expiresAt]);
                    
                    if ($result) {
                        return $token;
                    }
                    
                    $errorInfo = $stmt->errorInfo();
                    // If duplicate token error, generate new token and retry
                    if ($errorInfo[0] === '23000' && $retryCount < $maxRetries - 1) {
                        $token = bin2hex(random_bytes(32));
                        $retryCount++;
                        continue;
                    }
                    
                    error_log("Token insertion failed for user_id: $userId | Error: " . json_encode($errorInfo));
                    return null;
                } catch (\PDOException $e) {
                    // If duplicate token error, generate new token and retry
                    if ($e->getCode() === '23000' && $retryCount < $maxRetries - 1) {
                        $token = bin2hex(random_bytes(32));
                        $retryCount++;
                        continue;
                    }
                    throw $e;
                }
            }
            
            return null;
        } catch (\PDOException $e) {
            error_log("Token generation PDO error: " . $e->getMessage() . " | Code: " . $e->getCode() . " | SQL State: " . $e->errorInfo[0] ?? 'N/A');
            return null;
        } catch (\Exception $e) {
            error_log("Token generation failed: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine());
            return null;
        }
    }

    public static function verifyToken($token)
    {
        if (empty($token)) {
            return null;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT ut.*, u.id, u.username, u.email, u.role FROM user_tokens ut 
                             INNER JOIN users u ON ut.user_id = u.id 
                             WHERE ut.token = ? AND ut.expires_at > NOW() AND ut.revoked = 0");
        $stmt->execute([$token]);
        $result = $stmt->fetch();

        if ($result) {
            return [
                'id' => $result['user_id'],
                'username' => $result['username'],
                'email' => $result['email'],
                'role' => $result['role'] ?? 'user',
            ];
        }

        return null;
    }

    public static function revokeToken($token)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE user_tokens SET revoked = 1 WHERE token = ?");
        $stmt->execute([$token]);
    }

    public static function getCurrentUser()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }

        $token = $matches[1];
        return self::verifyToken($token);
    }

    public static function requireAuth()
    {
        $user = self::getCurrentUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        return $user;
    }

    public static function cleanupExpiredTokens()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM user_tokens WHERE expires_at < NOW() OR revoked = 1");
        $stmt->execute();
    }

    public static function requireAdmin()
    {
        $user = self::requireAuth();
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden: Admin access required']);
            exit;
        }
        return $user;
    }

    public static function isAdmin()
    {
        $user = self::getCurrentUser();
        return $user && ($user['role'] ?? 'user') === 'admin';
    }
}
