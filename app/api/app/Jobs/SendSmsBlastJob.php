<?php

namespace App\Jobs;

use App\Services\SmsBlastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsBlastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $recipientId;
    protected $message;
    protected $line;

    public function __construct($recipientId, $message, $line = null)
    {
        $this->recipientId = $recipientId;
        $this->message = $message;
        $this->line = $line;
    }

    public function handle(SmsBlastService $blastService)
    {
        Log::info("Processing SMS blast recipient ID: {$this->recipientId}");
        $blastService->processBatchRecipient($this->recipientId, $this->message, $this->line);
    }

    public function failed(\Throwable $exception)
    {
        Log::error("SMS blast job failed for recipient ID {$this->recipientId}: " . $exception->getMessage());
    }
}
