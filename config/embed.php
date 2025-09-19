<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed Domains for Embedding
    |--------------------------------------------------------------------------
    |
    | List of domains that are allowed to embed your forms.
    | Use full URLs with protocol (https://example.com)
    | Supports wildcards: 'https://*.example.com'
    |
    */
    'allowed_domains' => env('EMBED_ALLOWED_DOMAINS') ?
        explode(',', env('EMBED_ALLOWED_DOMAINS')) :
        [
            // 'https://client-site1.com',
            // 'https://client-site2.com',
            // 'https://*.trusted-domain.com', // wildcard support
        ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'max_attempts' => env('EMBED_RATE_LIMIT_ATTEMPTS', 10),
        'decay_minutes' => env('EMBED_RATE_LIMIT_DECAY', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Styles
    |--------------------------------------------------------------------------
    */
    'default_styles' => [
        'primary_color' => '#4CAF50',
        'font_family' => 'system-ui, -apple-system, sans-serif',
        'border_radius' => '4px',
    ],
];
