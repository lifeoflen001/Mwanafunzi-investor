<?php

return [
    // Keep local development and automated tests on their existing hosts by default.
    'enabled' => env('PORTAL_SUBDOMAINS_ENABLED', env('APP_ENV') === 'production'),
    'public_url' => env('PORTAL_PUBLIC_URL', 'https://mwanafunziinvestor.com'),
    'client_url' => env('PORTAL_CLIENT_URL', 'https://client.mwanafunziinvestor.com'),
    'admin_url' => env('PORTAL_ADMIN_URL', 'https://admin.mwanafunziinvestor.com'),
];
