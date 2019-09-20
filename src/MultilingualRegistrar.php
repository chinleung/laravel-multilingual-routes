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

        foreach ($locales as $locale) {
            $collection->add(
                $this
                    ->registerRoute($key, $handle, $locale, $options)
                    ->name($this->generateNameForLocaleFromOptions($locale, $key, $options))
            );
        }

        return $collection;
    }

    /**
     * Register a single route.
     *
     * @param  string  $key
     * @param  mixed  $handle
     * @param  string  $locale
     * @param  array  $options
     * @return \Illuminate\Routing\Route
     */
    protected function registerRoute(string $key, $handle, string $locale, array $options) : Route
    {
        $route = $this->router->addRoute(
            $this->getRequestMethodFromOptions($options),
            $this->applyUniqueRegistrationKey(
                $this->generateUriFromKey($key, $locale),
                $locale
            ),
            $handle
        );

        if ($prefix = $this->generatePrefixForLocale($key, $locale)) {
            $route->setUri("{$prefix}/{$route->uri}");
        }

        return $this->cleanUniqueRegistrationKey($route, $locale);
    }

    /**
     * Retrieve the request method from the options.
     *
     * @param  array  $options
     * @return array
     */
    protected function getRequestMethodFromOptions(array $options) : array
    {
        $method = $options['method'] ?? 'get';

        if ($method == 'get') {
            return ['GET', 'HEAD'];
        }

        return [strtoupper($method)];
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
     * Generate the route uri from the translation key and locale.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return string
     */
    protected function generateUriFromKey(string $key, string $locale) : string
    {
        if ($key == '/') {
            return $locale == config('app.fallback_locale') ? '/' : "/$locale";
        }

        return trans("routes.$key", [], $locale);
    }

    /**
     * Apply the unique registration key to make sure the route is registered.
     *
     * @param  string  $uri
     * @param  string  $locale
     * @return string
     */
    protected function applyUniqueRegistrationKey(string $uri, string $locale) : string
    {
        return "__{$locale}__".$uri;
    }

    /**
     * Clean the unique registration key from the route uri after it has been
     * registered in the router.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  string  $locale
     * @return \Illuminate\Routing\Route
     */
    protected function cleanUniqueRegistrationKey(Route $route, string $locale) : Route
    {
        return $route->setUri(str_replace("__{$locale}__", '', $route->uri));
    }
}
