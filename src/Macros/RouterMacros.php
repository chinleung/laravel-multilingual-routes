<?php

namespace ChinLeung\LaravelMultilingualRoutes\Macros;

use ChinLeung\LaravelMultilingualRoutes\MultilingualRegistrar;
use ChinLeung\LaravelMultilingualRoutes\MultilingualRoutePendingRegistration;
use Closure;

class RouterMacros
{
    /**
     * Register a multilingual GET route.
     *
     * @param  string  $key
     * @param  mixed  $handle
     * @param  array  $locales
     * @return \Closure
     */
    public function multilingual(): Closure
    {
        return function ($key, $handle = null, $locales = []) {
            return new MultilingualRoutePendingRegistration(
                $this->container && $this->container->bound(MultilingualRegistrar::class)
                    ? $this->container->make(MultilingualRegistrar::class)
                    : new MultilingualRegistrar($this),
                $key,
                $handle,
                $locales ?: locales()
            );
        };
    }
}
