<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (! function_exists('current_route')) {
    /**
     * Retrieve the current route in another locale.
     *
     * @param  string|null  $locale
     * @param  string|null  $fallback
     * @param  bool  $absolute
     * @return string
     */
    function current_route(string $locale = null, string $fallback = null, bool $absolute = true): string
    {
        if (is_null($fallback)) {
            $fallback = url(request()->server('REQUEST_URI'));
        }

        $route = Route::getCurrentRoute();
        $name = Str::replaceFirst(
            locale().'.',
            "{$locale}.",
            $route->getName()
        );

        if (! $name || ! in_array($locale, locales()) || ! Route::has($name)) {
            return $fallback;
        }

        return route($name, array_merge(
            (array) $route->parameters,
            (array) request()->getQueryString()
        ), $absolute);
    }
}

if (! function_exists('localized_route')) {
    /**
     * Retrieve a localized route.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  string|null  $locale
     * @param  bool  $absolute
     * @return string
     */
    function localized_route(string $name, $parameters = [], string $locale = null, bool $absolute = true): string
    {
        if (! in_array($locale, locales())) {
            $locale = locale();
        }

        return route("$locale.$name", $parameters, $absolute);
    }
}
