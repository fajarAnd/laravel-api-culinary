<?php

return [
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    ],

    'zomato' => [
        'api_key' => env('ZOMATO_API_KEY'),
        'base_url' => env('ZOMATO_BASE_URL', 'https://developers.zomato.com/api/v2.1'),
    ],
];