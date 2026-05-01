<?php

use Carbon\CarbonInterval;

return [
    'guard' => 'api',

    'private_key' => env('PASSPORT_PRIVATE_KEY'),
    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    'connection' => env('PASSPORT_CONNECTION'),

    'client_uuids' => true,

    'personal_access_client' => [
        'id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
        'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
    ],

    'tokens_expire_in' => CarbonInterval::days(1),
    'refresh_tokens_expire_in' => CarbonInterval::days(30),
    'personal_access_tokens_expire_in' => CarbonInterval::days(1),
];