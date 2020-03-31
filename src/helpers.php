<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (! function_exists('current_route')) {
    /**
     * Retrieve the current route in another locale.
     *
     * @param  string  $locale
     * @return string
     */
    function current_route(string $locale = null): string
    {
        $route = Route::getCurrentRoute();
        $parameters = request()->route()->parameters;
        $query = request()->getQueryString() ?: [];

        if (! $route->getName() || ! in_array($locale, locales())) {
            return url($route->uri.($query ? "?{$query}" : null));
        }

        return localized_route(
            Str::replaceFirst(locale().'.', null, $route->getName()),
            array_merge($parameters, $query),
            $locale
        );
    }
}

if (! function_exists('localized_route')) {
    /**
     * Retrieve a localized route.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  string  $locale
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
