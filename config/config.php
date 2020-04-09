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
    | The configuration option that defines if the routes of the default
    | locale should be prefixed.
    |
    */

    'prefix_default' => env('MULTILINGUAL_ROUTES_PREFIX_DEFAULT', false),

    /*
    |--------------------------------------------------------------------------
    | Home Prefix Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration option defines if the home route of the default locale
    | should be prefixed.
    |
    */

    'prefix_default_home' => env('MULTILINGUAL_ROUTES_PREFIX_DEFAULT_HOME', false),

    /*
    |--------------------------------------------------------------------------
    | Name Prefix Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration option that defines if the route name prefix should
    | be before the locale.
    |
    */

    'name_prefix_before_locale' => env('MULTILINGUAL_ROUTES_NAME_PREFIX_BEFORE_LOCALE', false),
];
