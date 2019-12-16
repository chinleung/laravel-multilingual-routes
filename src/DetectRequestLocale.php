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
        if ($locale = $this->getLocaleFromRequest($request)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Guess the locale from request query, path or headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function getLocaleFromRequest($request): ?string
    {
        $segment = $request->query('locale') ?: $request->segment(1);

        if ($segment && in_array($segment, locales())) {
            return $segment;
        }

        return $request->getPreferredLanguage(locales());
    }
}
