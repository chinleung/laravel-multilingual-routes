<?php

namespace ChinLeung\LaravelMultilingualRoutes;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

class MultilingualRegistrar
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Constructor of the class.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register the routes.
     *
     * @param  string  $key
     * @param  mixed  $handle
     * @param  array  $locales
     * @param  array  $options
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register(string $key, $handle, array $locales, array $options) : RouteCollection
    {
        $collection = new RouteCollection;

        $method = $this->getRequestMethodFromOptions($options);

        foreach ($locales as $locale) {
            $collection->add(
                $this
                    ->registerRoute($method, $key, $handle, $locale, $options)
                    ->name($this->generateNameForLocaleFromOptions($locale, $key, $options))
            );
        }

        return $collection;
    }

    /**
     * Register a single route.
     *
     * @param  string  $method
     * @param  string  $key
     * @param  mixed  $handle
     * @param  string  $locale
     * @param  array  $options
     * @return \Illuminate\Routing\Route
     */
    protected function registerRoute(string $method, string $key, $handle, string $locale, array $options) : Route
    {
        $route = $this->generateRoute($key, $locale);

        if (is_null($handle)) {
            return $this->router->view($route, Arr::get($options, 'view', $key));
        }

        return $this->router->{strtolower($method)}(
            $route,
            $handle
        );
    }

    /**
     * Retrieve the request method from the options.
     *
     * @param  array  $options
     * @return string
     */
    protected function getRequestMethodFromOptions(array $options) : string
    {
        return $options['method'] ?? 'get';
    }

    /**
     * Generate the name of the route based on the options.
     *
     * @param  string  $locale
     * @param  string  $key
     * @param  array  $options
     * @return string
     */
    protected function generateNameForLocaleFromOptions(string $locale, string $key, array $options) : string
    {
        if ($name = Arr::get($options, "names.$locale")) {
            return "$locale.$name";
        }

        return sprintf(
            '%s.%s',
            $locale,
            Arr::get($options, 'name', $key)
        );
    }

    /**
     * Generate the prefix of the route based on the options.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return string|null
     */
    protected function generatePrefixForLocale(string $key, string $locale) : ?string
    {
        if ($key == '/') {
            return null;
        }

        if ($locale != config('app.fallback_locale')) {
            return $locale;
        }

        return config('laravel-multilingual-routes.prefix_fallback')
            ? $locale
            : null;
    }

    /**
     * Generate a route from the key and locale.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return string
     */
    protected function generateRoute(string $key, string $locale) : string
    {
        if ($key == '/') {
            $route = $locale == config('app.fallback_locale') ? '/' : "/$locale";
        } else {
            $route = trans("routes.$key", [], $locale);
        }

        if ($prefix = $this->generatePrefixForLocale($key, $locale)) {
            $route = "{$prefix}/{$route}";
        }

        return $route;
    }
}
