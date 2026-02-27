<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->text('message_text');
            $table->timestamp('received_at');
            $table->string('goip_date')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['phone_number', 'received_at']);
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
