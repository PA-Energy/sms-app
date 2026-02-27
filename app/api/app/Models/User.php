<?php

namespace App\Models;

use App\Core\Database;

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

    public static function create($username, $email, $password)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO users (username, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        return $db->lastInsertId();
    }
}
