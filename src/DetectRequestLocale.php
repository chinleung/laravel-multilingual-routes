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
        if ($locale = $request->getPreferredLanguage(locales())) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
