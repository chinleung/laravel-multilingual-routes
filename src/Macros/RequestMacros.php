<?php

namespace ChinLeung\MultilingualRoutes\Macros;

use Closure;

class RequestMacros
{
    /**
     * Determine if the route name matches a given pattern in the current
     * locale.
     *
     * @param  mixed  ...$patterns
     * @return \Closure
     */
    public function localizedRouteIs(): Closure
    {
        return function (...$patterns) {
            return $this->routeIs(array_map(static fn($pattern) => locale().".{$pattern}", $patterns));
        };
    }
}
