<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SmsBlastController;
use App\Http\Controllers\SmsInboxController;
use App\Http\Controllers\SmsOutboxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // SMS Inbox routes
    Route::get('/sms/inbox', [SmsInboxController::class, 'index']);
    Route::post('/sms/inbox/sync', [SmsInboxController::class, 'sync']);
    Route::put('/sms/inbox/{id}/read', [SmsInboxController::class, 'markAsRead']);
    Route::put('/sms/inbox/read-all', [SmsInboxController::class, 'markAllAsRead']);
    Route::get('/sms/inbox/{id}', [SmsInboxController::class, 'show']);

    // SMS Outbox routes
    Route::get('/sms/outbox', [SmsOutboxController::class, 'index']);
    Route::post('/sms/send', [SmsOutboxController::class, 'send']);
    Route::get('/sms/outbox/{id}', [SmsOutboxController::class, 'show']);

    // SMS Blast routes
    Route::get('/sms/blast', [SmsBlastController::class, 'index']);
    Route::post('/sms/blast', [SmsBlastController::class, 'create']);
    Route::post('/sms/blast/upload-csv', [SmsBlastController::class, 'uploadCsv']);
    Route::get('/sms/blast/{id}', [SmsBlastController::class, 'show']);
    Route::get('/sms/blast/{id}/progress', [SmsBlastController::class, 'progress']);
});
