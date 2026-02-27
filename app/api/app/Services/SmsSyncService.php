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
            $messages = $this->goIpService->fetchInbox();
            $syncedCount = 0;
            $skippedCount = 0;

            foreach ($messages as $message) {
                $receivedAt = $this->parseGoIpDate($message['date']);

                if (!SmsMessage::exists($message['phone'], $message['text'], $receivedAt)) {
                    SmsMessage::create($message['phone'], $message['text'], $receivedAt, $message['date']);
                    $syncedCount++;
                } else {
                    $skippedCount++;
                }
            }

            return [
                'success' => true,
                'synced' => $syncedCount,
                'skipped' => $skippedCount,
                'total' => count($messages),
            ];
        } catch (\Exception $e) {
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
