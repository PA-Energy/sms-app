<?php

namespace App\Services;

use App\Models\SmsBatch;
use App\Models\SmsBatchRecipient;
use App\Services\GoIpService;

class SmsBlastService
{
    protected $goIpService;

    public function __construct()
    {
        $this->goIpService = new GoIpService();
    }

    public function createBatch($userId, $name, $phoneNumbers, $message, $line = null)
    {
        $batchId = SmsBatch::create([
            'user_id' => $userId,
            'name' => $name,
            'total_recipients' => count($phoneNumbers),
        ]);

        foreach ($phoneNumbers as $phone) {
            SmsBatchRecipient::create($batchId, trim($phone));
        }

        // Process batch in background (simple approach - in production use proper queue)
        $this->processBatch($batchId, $message, $line);

        return SmsBatch::findById($batchId);
    }

    protected function processBatch($batchId, $message, $line = null)
    {
        // Simple processing - in production, use a proper queue system
        $recipients = SmsBatchRecipient::findPending($batchId, 100);
        
        foreach ($recipients as $recipient) {
            try {
                $result = $this->goIpService->sendSms($recipient['phone_number'], $message, $line);
                
                if ($result['success']) {
                    SmsBatchRecipient::updateStatus($recipient['id'], 'sent');
                    SmsBatch::incrementSent($batchId);
                } else {
                    SmsBatchRecipient::updateStatus($recipient['id'], 'failed', $result['message']);
                    SmsBatch::incrementFailed($batchId);
                }
            } catch (\Exception $e) {
                SmsBatchRecipient::updateStatus($recipient['id'], 'failed', $e->getMessage());
                SmsBatch::incrementFailed($batchId);
            }
            
            // Small delay to avoid overwhelming the GoIP device
            usleep(500000); // 0.5 second
        }

        // Update batch status
        $batch = SmsBatch::findById($batchId);
        $totalProcessed = $batch['sent_count'] + $batch['failed_count'];
        if ($totalProcessed >= $batch['total_recipients']) {
            $status = $batch['failed_count'] === $batch['total_recipients'] ? 'failed' : 'completed';
            SmsBatch::updateStatus($batchId, $status);
        } else {
            SmsBatch::updateStatus($batchId, 'processing');
        }
    }

    public function getBatchProgress($batchId)
    {
        $batch = SmsBatch::findById($batchId);
        
        return [
            'id' => $batch['id'],
            'name' => $batch['name'],
            'status' => $batch['status'],
            'total' => $batch['total_recipients'],
            'sent' => $batch['sent_count'],
            'failed' => $batch['failed_count'],
            'pending' => $batch['total_recipients'] - $batch['sent_count'] - $batch['failed_count'],
            'progress' => $batch['total_recipients'] > 0
                ? round((($batch['sent_count'] + $batch['failed_count']) / $batch['total_recipients']) * 100, 2)
                : 0,
        ];
    }
}
