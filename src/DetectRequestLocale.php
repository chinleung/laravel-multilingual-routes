<?php

namespace ChinLeung\LaravelMultilingualRoutes;

use Closure;

class DetectRequestLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $segment = $request->segment(1);

        if (!empty($segment) && $segment !== $request->getLocale() && in_array($segment, locales())) {
            app()->setLocale($segment);
        }

        return $next($request);
    }
}
