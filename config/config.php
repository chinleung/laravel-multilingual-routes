<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The main locale of the routes.
    |
    */

    'default' => env('MULTILINGUAL_ROUTES_DEFAULT_LOCALE', config('app.fallback_locale')),

    /*
    |--------------------------------------------------------------------------
    | Prefix Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration option defines if the routes of the default locale
    | should be prefixed.
    |
    */

    'prefix_default' => env('MULTILINGUAL_ROUTES_PREFIX_DEFAULT', false),
];
