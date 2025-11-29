<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Supported Methods: Array of HTTP methods that are allowed to be executed
    | in cross-origin requests. You can also pass '*' to allow all methods.
    |
    | Supported Headers: Headers that are allowed in the actual request.
    | You can also pass '*' to allow all headers submitted.
    |
    | Max Age: How many seconds browser is allowed to cache preflight request
    | requests. Generally, the preflight request can be cached for a long time.
    |
    | Expose Headers: Headers that are safe to expose to the requesting client
    | in the actual response.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173,http://localhost:3000,http://127.0.0.1:5173')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Range', 'X-Content-Range'],

    'max_age' => 0,

    'supports_credentials' => true,

];
