<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function findByUsername($username)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($username, $email, $password, $role = 'user')
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        return $db->lastInsertId();
    }

    public static function getAll($page = 1, $perPage = 20, $search = '')
    {
        $db = Database::getInstance()->getConnection();
        $offset = ($page - 1) * $perPage;
        
        $where = [];
        $params = [];
        
        if ($search) {
            $where[] = "(username LIKE ? OR email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM users {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch()['total'];
        
        // Get data
        $limitClause = "LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $db->prepare("SELECT id, username, email, role, created_at, updated_at FROM users {$whereClause} ORDER BY created_at DESC {$limitClause}");
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $total > 0 ? ceil($total / $perPage) : 1,
        ];
    }

    public static function update($id, $username, $email, $role = null, $password = null)
    {
        $db = Database::getInstance()->getConnection();
        $updates = [];
        $params = [];
        
        if ($username !== null) {
            $updates[] = "username = ?";
            $params[] = $username;
        }
        if ($email !== null) {
            $updates[] = "email = ?";
            $params[] = $email;
        }
        if ($role !== null) {
            $updates[] = "role = ?";
            $params[] = $role;
        }
        if ($password !== null) {
            $updates[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $updates[] = "updated_at = NOW()";
        $params[] = $id;
        
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
