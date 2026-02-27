<?php

// Start output buffering to catch any unwanted output
ob_start();

require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Router;

$router = new Router();

// Auth routes
$router->post('/api/auth/login', 'AuthController@login');
$router->get('/api/auth/me', 'AuthController@me');
$router->post('/api/auth/logout', 'AuthController@logout');

// User Management routes (Admin only)
$router->get('/api/users', 'UserController@index');
$router->post('/api/users', 'UserController@store');
$router->get('/api/users/{id}', 'UserController@show');
$router->put('/api/users/{id}', 'UserController@update');
$router->delete('/api/users/{id}', 'UserController@destroy');

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

// Clear any output that might have been generated during bootstrap
ob_clean();

$router->dispatch();

// If we get here, no route matched - ensure clean output
ob_end_flush();
