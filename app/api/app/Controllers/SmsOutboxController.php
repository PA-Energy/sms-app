<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\SmsOutbox;
use App\Services\GoIpService;

class SmsOutboxController extends Controller
{
    public function index()
    {
        $user = Auth::requireAuth();
        
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;
        $status = $_GET['status'] ?? null;

        $result = SmsOutbox::getAll($user['id'], $page, $perPage, $status);
        
        return $this->json(['success' => true, 'data' => $result]);
    }

    public function send()
    {
        $user = Auth::requireAuth();
        $data = $this->getRequestData();
        
        $errors = $this->validate($data, [
            'phone_number' => 'required',
            'message' => 'required',
        ]);

        if (!empty($errors)) {
            return $this->json(['success' => false, 'errors' => $errors], 422);
        }

        $outbox = SmsOutbox::create([
            'user_id' => $user['id'],
            'phone_number' => $data['phone_number'],
            'message_text' => $data['message'],
            'goip_line' => $data['line'] ?? GOIP_LINE,
            'status' => 'pending',
        ]);

        try {
            $goIpService = new GoIpService();
            $result = $goIpService->sendSms($data['phone_number'], $data['message'], $data['line'] ?? null);

            if ($result['success']) {
                SmsOutbox::updateStatus($outbox, 'sent');
                return $this->json(['success' => true, 'message' => 'SMS sent successfully', 'data' => SmsOutbox::findById($outbox)]);
            } else {
                SmsOutbox::updateStatus($outbox, 'failed', $result['message']);
                return $this->json(['success' => false, 'message' => 'Failed to send SMS', 'error' => $result['message']], 500);
            }
        } catch (\Exception $e) {
            SmsOutbox::updateStatus($outbox, 'failed', $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to send SMS', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::requireAuth();
        $outbox = SmsOutbox::findByIdAndUser($id, $user['id']);
        
        if (!$outbox) {
            return $this->json(['success' => false, 'message' => 'Message not found'], 404);
        }

        return $this->json(['success' => true, 'data' => $outbox]);
    }
}
