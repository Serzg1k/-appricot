<?php

return [
    'cache_ttl'      => env('MOVIES_CACHE_TTL', 300),
    'retries'        => env('MOVIES_RETRIES', 3),
    'retry_sleep_ms' => env('MOVIES_RETRY_SLEEP_MS', 200),
];
