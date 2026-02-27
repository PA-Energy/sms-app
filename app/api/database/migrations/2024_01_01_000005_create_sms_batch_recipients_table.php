<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_batch_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('sms_batches')->onDelete('cascade');
            $table->string('phone_number');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('batch_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_batch_recipients');
    }
};
