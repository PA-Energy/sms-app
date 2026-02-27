<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'goip' => [
        'addr' => env('GOIP_ADDR', '192.168.1.3'),
        'user' => env('GOIP_USER', 'admin'),
        'password' => env('GOIP_PASSWORD', 'admin'),
        'port' => env('GOIP_PORT', ''),
        'line' => env('GOIP_LINE', 1),
    ],

];
