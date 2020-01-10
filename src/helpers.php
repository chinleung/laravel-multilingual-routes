<?php

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
            $locale = app()->getLocale();
        }

        return route("$locale.$name", $parameters, $absolute);
    }
}
