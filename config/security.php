<?php

return [
    'admin_mfa_required' => env('ADMIN_MFA_REQUIRED', env('APP_ENV') === 'production'),
    'admin_mfa_issuer' => env('ADMIN_MFA_ISSUER', env('APP_NAME', 'Mwanafunzi Investor')),
    'admin_mfa_recovery_codes' => 8,
    'admin_mfa_window' => 1,
    'outbound_connect_timeout' => (int) env('OUTBOUND_CONNECT_TIMEOUT', 10),
    'outbound_timeout' => (int) env('OUTBOUND_TIMEOUT', 20),
    'force_hsts' => (bool) env('FORCE_HSTS', false),
];
