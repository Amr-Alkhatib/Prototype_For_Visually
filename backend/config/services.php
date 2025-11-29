<?php

return [
    'tum_online' => [
        'api_url' => env('TUM_ONLINE_API_URL', 'https://campus.tum.de/tumonline/rest/v1'),
        'client_id' => env('TUM_ONLINE_CLIENT_ID'),
        'client_secret' => env('TUM_ONLINE_CLIENT_SECRET'),
    ],
];
