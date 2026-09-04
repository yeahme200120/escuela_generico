<?php
return [
    'url'     => env('PYTHON_SERVICE_URL', 'http://localhost:8001'),
    'secret'  => env('PYTHON_SERVICE_SECRET', ''),
    'timeout' => env('PYTHON_TIMEOUT', 300),
    'queue'   => env('PYTHON_QUEUE', 'python'),
];
