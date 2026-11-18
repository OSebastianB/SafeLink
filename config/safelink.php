<?php

return [
    'name' => 'SafeLink',
    'tagline' => 'Smart URL Risk Assessment System',

    /*
    |--------------------------------------------------------------------------
    | External providers
    |--------------------------------------------------------------------------
    | RDAP lookups are optional and disabled by default for safety and demos.
    | When enabled, only allowlisted RDAP endpoints are contacted — never the
    | user-supplied URL host.
    */
    'rdap_enabled' => env('SAFELINK_RDAP_ENABLED', false),
    'rdap_timeout' => env('SAFELINK_RDAP_TIMEOUT', 5),

    'threat_provider' => env('SAFELINK_THREAT_PROVIDER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Admin bootstrap
    |--------------------------------------------------------------------------
    */
    'admin_name' => env('SAFELINK_ADMIN_NAME', 'Administrator'),
    'admin_email' => env('SAFELINK_ADMIN_EMAIL', 'admin@safelink.test'),
    'admin_password' => env('SAFELINK_ADMIN_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Default risk thresholds
    |--------------------------------------------------------------------------
    */
    'risk_low_max' => 20,
    'risk_medium_max' => 50,

    'min_data_coverage_warning' => 50,
];
