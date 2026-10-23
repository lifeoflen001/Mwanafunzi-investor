<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tawk.to widget
    |--------------------------------------------------------------------------
    |
    | These identifiers are public widget configuration, not credentials. The
    | widget is included by the public layout only; admin and client portals
    | remain free of third-party chat scripts.
    |
    */
    'enabled' => filter_var(env('TAWK_ENABLED', true), FILTER_VALIDATE_BOOL),
    'property_id' => env('TAWK_PROPERTY_ID', '6ad8c28aa03d3a34b7a7c606'),
    'widget_id' => env('TAWK_WIDGET_ID', '1k5fbfsa8'),
];
