<?php

return [
    'provider' => env('PAYMENT_PROVIDER', 'sandbox'),
    'environment' => env('PAYMENT_ENVIRONMENT', 'sandbox'),
    'currency' => strtoupper(env('COMMERCE_CURRENCY', 'TZS')),
    'download_url_lifetime' => (int) env('DOWNLOAD_URL_LIFETIME', 10),
    'tax' => [
        'enabled' => (bool) env('COMMERCE_TAX_ENABLED', false),
        'label' => env('COMMERCE_TAX_LABEL', 'Tax'),
        'rate' => env('COMMERCE_TAX_RATE', '0'),
        'prices_include' => (bool) env('COMMERCE_PRICES_INCLUDE_TAX', false),
        'identifier' => env('BUSINESS_TAX_IDENTIFIER'),
    ],
];
