<?php

namespace App\Models;

use App\Core\Database;

class SmsBatch
{
    public static function getAll($userId, $page = 1, $perPage = 20)
    {
        $db = Database::getInstance()->getConnection();
        $offset = ($page - 1) * $perPage;
        
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM sms_batches WHERE user_id = ?");
        $countStmt->execute([$userId]);
        $total = $countStmt->fetch()['total'];
        
        $stmt = $db->prepare("SELECT * FROM sms_batches WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$userId, $perPage, $offset]);
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ];
    }

    public static function findById($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM sms_batches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByIdAndUser($id, $userId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM sms_batches WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO sms_batches (user_id, name, total_recipients, sent_count, failed_count, status, created_at, updated_at) VALUES (?, ?, ?, 0, 0, 'pending', NOW(), NOW())");
        $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['total_recipients'],
        ]);
        return $db->lastInsertId();
    }

    public static function updateStatus($id, $status)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE sms_batches SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public static function incrementSent($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE sms_batches SET sent_count = sent_count + 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function incrementFailed($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE sms_batches SET failed_count = failed_count + 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}
