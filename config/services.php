<?php return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'python' => [
        'url' => env('PYTHON_API_URL', 'http://localhost:8001'),
        'secret' => env('PYTHON_API_SECRET', 'dev-secret-key'),
        'timeout' => env('PYTHON_API_TIMEOUT', 300),
    ],
];
