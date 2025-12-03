<?php

return [
    'paths' => ['api/*', 'auth/*'], // Sesuaikan dengan rute yang ingin Anda izinkan
    'allowed_methods' => ['*'], // Atau spesifik seperti ['GET', 'POST', 'PUT', 'DELETE']
    'allowed_origins' => ['https://fe-tokoonline.vercel.app'],// Izinkan origin frontend Anda
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];

