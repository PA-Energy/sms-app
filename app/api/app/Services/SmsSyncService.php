<?php

namespace App\Services;

use App\Models\SmsMessage;
use App\Services\GoIpService;

class SmsSyncService
{
    protected $goIpService;

    public function __construct()
    {
        $this->goIpService = new GoIpService();
    }

    public function syncInbox()
    {
        try {
            // Fetch all messages from GoIP
            $messages = $this->goIpService->fetchInbox();
            
            // Delete all existing messages to avoid duplicates
            SmsMessage::deleteAll();
            
            // Insert all messages from GoIP
            $syncedCount = 0;
            foreach ($messages as $message) {
                $receivedAt = $this->parseGoIpDate($message['date']);
                SmsMessage::create($message['phone'], $message['text'], $receivedAt, $message['date']);
                $syncedCount++;
            }

            return [
                'success' => true,
                'synced' => $syncedCount,
                'deleted' => true,
                'total' => count($messages),
                'message' => "Synced {$syncedCount} messages from GoIP device"
            ];
        } catch (\Exception $e) {
            error_log("SmsSyncService::syncInbox error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function parseGoIpDate($dateString)
    {
        $formats = ['Y-m-d H:i:s', 'Y/m/d H:i:s', 'd-m-Y H:i:s', 'd/m/Y H:i:s'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date) {
                return $date->format('Y-m-d H:i:s');
            }
        }
        
        return date('Y-m-d H:i:s');
    }
}
