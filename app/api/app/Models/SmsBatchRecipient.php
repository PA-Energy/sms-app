<?php

namespace App\Models;

use App\Core\Database;

class SmsBatchRecipient
{
    public static function create($batchId, $phoneNumber)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO sms_batch_recipients (batch_id, phone_number, status, created_at, updated_at) VALUES (?, ?, 'pending', NOW(), NOW())");
        $stmt->execute([$batchId, $phoneNumber]);
        return $db->lastInsertId();
    }

    public static function findByBatch($batchId)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM sms_batch_recipients WHERE batch_id = ?");
        $stmt->execute([$batchId]);
        return $stmt->fetchAll();
    }

    public static function updateStatus($id, $status, $errorMessage = null)
    {
        $db = Database::getInstance()->getConnection();
        if ($status === 'sent') {
            $stmt = $db->prepare("UPDATE sms_batch_recipients SET status = ?, sent_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $id]);
        } else {
            $stmt = $db->prepare("UPDATE sms_batch_recipients SET status = ?, error_message = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $errorMessage, $id]);
        }
    }

    public static function findPending($batchId, $limit = 10)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM sms_batch_recipients WHERE batch_id = ? AND status = 'pending' LIMIT ?");
        $stmt->execute([$batchId, $limit]);
        return $stmt->fetchAll();
    }
}
