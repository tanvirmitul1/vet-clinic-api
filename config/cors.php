<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    // With credentials=true, we cannot use '*'. Use patterns to allow any http/https domain
    'allowed_origins' => [],
    'allowed_origins_patterns' => ['#^https?://.+$#'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    // Keep credentials allowed for cookie-based auth
    'supports_credentials' => true,
];
