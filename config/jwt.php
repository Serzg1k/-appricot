<?php

return [
    'secret' => env('JWT_SECRET', 'your-256-bit-secret'),
    'ttl'    => env('JWT_TTL', 43200),
];
