<?php

namespace App\Models;

use App\Core\Database;

class SmsMessage
{
    public static function getAll($page = 1, $perPage = 20, $search = '', $isRead = null)
    {
        $db = Database::getInstance()->getConnection();
        $offset = ($page - 1) * $perPage;
        
        $where = [];
        $params = [];
        
        if ($search) {
            $where[] = "(phone_number LIKE ? OR message_text LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        if ($isRead !== null) {
            $where[] = "is_read = ?";
            $params[] = $isRead ? 1 : 0;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM sms_messages {$whereClause}");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get data
        $stmt = $db->prepare("SELECT * FROM sms_messages {$whereClause} ORDER BY received_at DESC LIMIT ? OFFSET ?");
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
        $stmt = $db->prepare("SELECT * FROM sms_messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($phoneNumber, $messageText, $receivedAt, $goipDate)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO sms_messages (phone_number, message_text, received_at, goip_date, synced_at, is_read, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), 0, NOW(), NOW())");
        $stmt->execute([$phoneNumber, $messageText, $receivedAt, $goipDate]);
        return $db->lastInsertId();
    }

    public static function exists($phoneNumber, $messageText, $receivedAt)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM sms_messages WHERE phone_number = ? AND message_text = ? AND received_at = ?");
        $stmt->execute([$phoneNumber, $messageText, $receivedAt]);
        return $stmt->fetch()['count'] > 0;
    }

    public static function markAsRead($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE sms_messages SET is_read = 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function markAllAsRead()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE sms_messages SET is_read = 1, updated_at = NOW() WHERE is_read = 0");
        $stmt->execute();
    }
}
