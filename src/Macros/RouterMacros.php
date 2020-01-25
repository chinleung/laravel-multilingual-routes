<?php

namespace ChinLeung\MultilingualRoutes\Macros;

use ChinLeung\MultilingualRoutes\MultilingualRegistrar;
use ChinLeung\MultilingualRoutes\MultilingualRoutePendingRegistration;
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
                ltrim($key, '/'),
                $handle,
                $locales ?: locales()
            );
        };
    }
}
