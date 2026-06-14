<?php

return [
    'headers' => [
        'enabled' => (bool) env('SECURITY_HEADERS_ENABLED', true),
        'frame_options' => env('SECURITY_FRAME_OPTIONS', 'SAMEORIGIN'),
        'content_type_options' => env('SECURITY_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'permissions_policy' => env(
            'SECURITY_PERMISSIONS_POLICY',
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()'
        ),
    ],

    'hsts' => [
        'enabled' => (bool) env('SECURITY_HSTS_ENABLED', false),
        'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
        'include_subdomains' => (bool) env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => (bool) env('SECURITY_HSTS_PRELOAD', false),
    ],

    'csp' => [
        'enabled' => (bool) env('SECURITY_CSP_ENABLED', true),
        'form_action' => env('SECURITY_CSP_FORM_ACTION', "'self'"),
        'form_action_extra' => array_values(array_filter(array_map(
            static fn (string $value): string => trim($value),
            explode(',', (string) env('SECURITY_CSP_FORM_ACTION_EXTRA', ''))
        ))),
        'policy' => env(
            'SECURITY_CSP_POLICY',
            "default-src 'self'; base-uri 'self'; frame-ancestors 'self'; object-src 'none'; ".
            "img-src 'self' data: https: blob:; ".
            "script-src 'self' 'unsafe-inline'; ".
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://fonts.bunny.net; ".
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://fonts.bunny.net; ".
            "connect-src 'self' https://viacep.com.br; frame-src 'self'"
        ),
    ],
];
