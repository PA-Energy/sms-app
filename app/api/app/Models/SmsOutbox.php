<?php

namespace App\Models;

use App\Core\Database;

class SmsOutbox
{
    public static function getAll($userId, $page = 1, $perPage = 20, $status = null)
    {
        $db = Database::getInstance()->getConnection();
        $offset = ($page - 1) * $perPage;
        
        $where = ['user_id = ?'];
        $params = [$userId];
        
        if ($status) {
            $where[] = "status = ?";
            $params[] = $status;
        }
        
        $whereClause = "WHERE " . implode(" AND ", $where);
        
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM sms_outbox {$whereClause}");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        $stmt = $db->prepare("SELECT * FROM sms_outbox {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $params[] = $perPage;
        $params[] = $offset;
        $stmt->execute($params);
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
        $stmt = $db->prepare("SELECT * FROM sms_outbox WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByIdAndUser($id, $userId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM sms_outbox WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO sms_outbox (user_id, phone_number, message_text, goip_line, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([
            $data['user_id'],
            $data['phone_number'],
            $data['message_text'],
            $data['goip_line'],
            $data['status'],
        ]);
        return $db->lastInsertId();
    }

    public static function updateStatus($id, $status, $errorMessage = null)
    {
        $db = Database::getInstance()->getConnection();
        if ($status === 'sent') {
            $stmt = $db->prepare("UPDATE sms_outbox SET status = ?, sent_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $id]);
        } else {
            $stmt = $db->prepare("UPDATE sms_outbox SET status = ?, error_message = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $errorMessage, $id]);
        }
    }
}
