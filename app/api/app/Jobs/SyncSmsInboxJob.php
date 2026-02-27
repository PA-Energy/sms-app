<?php

namespace App\Jobs;

use App\Services\SmsSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSmsInboxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(SmsSyncService $syncService)
    {
        Log::info('Starting scheduled SMS inbox sync');
        $result = $syncService->syncInbox();
        
        if ($result['success']) {
            Log::info("Scheduled sync completed: {$result['synced']} new messages synced");
        } else {
            Log::error("Scheduled sync failed: {$result['error']}");
        }
    }
}
