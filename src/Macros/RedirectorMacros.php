<?php

namespace ChinLeung\LaravelMultilingualRoutes\Macros;

use Closure;

class RedirectorMacros
{
    /**
     * Create a new redirect response to a named route.
     *
     * @param  string  $route
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function localizedRoute(): Closure
    {
        return function (string $route, $parameters = [], int $status = 302, array $headers = []) {
            return $this->route(locale().".{$route}", $parameters, $status, $headers);
        };
    }
}
