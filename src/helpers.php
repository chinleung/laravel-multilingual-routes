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

        if (! $route->getName() || ! in_array($locale, locales())) {
            return url(request()->server('REQUEST_URI'));
        }

        return localized_route(
            Str::replaceFirst(locale().'.', null, $route->getName()),
            array_merge(
                (array) $route->parameters,
                (array) request()->getQueryString()
            ),
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
