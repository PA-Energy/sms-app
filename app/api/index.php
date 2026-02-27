<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Router;

$router = new Router();

// Auth routes
$router->post('/api/auth/login', 'AuthController@login');
$router->get('/api/auth/me', 'AuthController@me');
$router->post('/api/auth/logout', 'AuthController@logout');

// SMS Inbox routes
$router->get('/api/sms/inbox', 'SmsInboxController@index');
$router->post('/api/sms/inbox/sync', 'SmsInboxController@sync');
$router->put('/api/sms/inbox/{id}/read', 'SmsInboxController@markAsRead');
$router->put('/api/sms/inbox/read-all', 'SmsInboxController@markAllAsRead');
$router->get('/api/sms/inbox/{id}', 'SmsInboxController@show');

// SMS Outbox routes
$router->get('/api/sms/outbox', 'SmsOutboxController@index');
$router->post('/api/sms/send', 'SmsOutboxController@send');
$router->get('/api/sms/outbox/{id}', 'SmsOutboxController@show');

// SMS Blast routes
$router->get('/api/sms/blast', 'SmsBlastController@index');
$router->post('/api/sms/blast', 'SmsBlastController@create');
$router->post('/api/sms/blast/upload-csv', 'SmsBlastController@uploadCsv');
$router->get('/api/sms/blast/{id}', 'SmsBlastController@show');
$router->get('/api/sms/blast/{id}/progress', 'SmsBlastController@progress');

// Health check
$router->get('/api/health', function() {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
    exit;
});

// Cleanup expired tokens (can be called periodically)
$router->get('/api/cleanup-tokens', function() {
    \App\Core\Auth::cleanupExpiredTokens();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'message' => 'Expired tokens cleaned up']);
    exit;
});

$router->dispatch();
