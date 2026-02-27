<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\SmsMessage;
use App\Services\SmsSyncService;

class SmsInboxController extends Controller
{
    public function index()
    {
        Auth::requireAuth();
        
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;
        $search = $_GET['search'] ?? '';
        $isRead = isset($_GET['is_read']) ? (bool)$_GET['is_read'] : null;

        $result = SmsMessage::getAll($page, $perPage, $search, $isRead);
        
        return $this->json(['success' => true, 'data' => $result]);
    }

    public function sync()
    {
        Auth::requireAuth();
        
        $syncService = new SmsSyncService();
        $result = $syncService->syncInbox();
        
        return $this->json($result, $result['success'] ? 200 : 500);
    }

    public function markAsRead($id)
    {
        Auth::requireAuth();
        
        SmsMessage::markAsRead($id);
        return $this->json(['success' => true, 'message' => 'Message marked as read']);
    }

    public function markAllAsRead()
    {
        Auth::requireAuth();
        
        SmsMessage::markAllAsRead();
        return $this->json(['success' => true, 'message' => 'All messages marked as read']);
    }

    public function show($id)
    {
        Auth::requireAuth();
        
        $message = SmsMessage::findById($id);
        if (!$message) {
            return $this->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        if (!$message['is_read']) {
            SmsMessage::markAsRead($id);
        }

        return $this->json(['success' => true, 'data' => $message]);
    }
}
