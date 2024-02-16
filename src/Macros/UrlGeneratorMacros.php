<?php

namespace ChinLeung\MultilingualRoutes\Macros;

use Closure;

class UrlGeneratorMacros
{
    /**
     * Create a temporary signed route URL for a named route in the current
     * locale.
     *
     * @param  string  $name
     * @param  \DateTimeInterface|\DateInterval|int  $expiration
     * @param  array  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function temporarySignedLocalizedRoute(): Closure
    {
        return function ($name, $expiration, $parameters = [], $absolute = true) {
            return $this->signedLocalizedRoute($name, $parameters, $expiration, $absolute);
        };
    }

    /**
     * Create a signed route URL for a named route in the current
     * locale.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function signedLocalizedRoute()
    {
        return function ($name, $parameters = [], $expiration = null, $absolute = true) {
            return $this->signedRoute(locale().".{$name}", $parameters, $expiration, $absolute);
        };
    }
}
