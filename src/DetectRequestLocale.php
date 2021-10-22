<?php

namespace ChinLeung\MultilingualRoutes;

use Closure;
use Symfony\Component\HttpFoundation\Request;

class DetectRequestLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $segment = $request->locale ?: $request->segment(1);

        if (in_array($segment, locales(), false)) {
            app()->setLocale($segment);
        }

        return $next($request);
    }
}
