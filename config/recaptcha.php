<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA
    |--------------------------------------------------------------------------
    |
    | Keep the secret in the deployment environment where possible. The admin
    | security screen may store an encrypted fallback in site_settings for
    | installations that do not have an environment secret manager.
    |
    */
    'enabled' => (bool) env('RECAPTCHA_ENABLED', false),
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'version' => env('RECAPTCHA_VERSION', 'v3'),
    'min_score' => (float) env('RECAPTCHA_MIN_SCORE', 0.5),
    'hostname' => env('RECAPTCHA_HOSTNAME'),
    'timeout' => (int) env('RECAPTCHA_TIMEOUT', 5),
    'forms' => [
        'admin_login' => (bool) env('RECAPTCHA_FORM_ADMIN_LOGIN', true),
        'login' => (bool) env('RECAPTCHA_FORM_LOGIN', true),
        'register' => (bool) env('RECAPTCHA_FORM_REGISTER', true),
        'password_reset' => (bool) env('RECAPTCHA_FORM_PASSWORD_RESET', true),
        'contact' => (bool) env('RECAPTCHA_FORM_CONTACT', true),
        'comments' => (bool) env('RECAPTCHA_FORM_COMMENTS', true),
        'waitlist' => (bool) env('RECAPTCHA_FORM_WAITLIST', true),
        'checkout' => (bool) env('RECAPTCHA_FORM_CHECKOUT', true),
    ],
];
