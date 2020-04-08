<?php

namespace ChinLeung\MultilingualRoutes;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

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
    public function register(string $key, $handle, array $locales, array $options): RouteCollection
    {
        foreach ($locales as $locale) {
            $route = $this->registerRoute($key, $handle, $locale, $options);

            if (isset($options['defaults']) && is_array($options['defaults'])) {
                foreach ($options['defaults'] as $paramKey => $paramValue) {
                    $route->defaults($paramKey, $paramValue);
                }
            }
        }

        return tap($this->router->getRoutes())->refreshNameLookups();
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
    protected function registerRoute(string $key, $handle, string $locale, array $options): Route
    {
        $route = $this->generateRoute($key, $handle, $locale, $options);

        foreach (Arr::get($options, 'constraints', []) as $name => $expression) {
            $route->where($name, $expression);
        }

        if ($prefix = $this->generatePrefixForLocale($key, $locale)) {
            $route->setUri("{$prefix}/{$route->uri}");
        }

        data_set($route, 'action.as', (
            $this->generateNameForLocaleFromOptions(
                $locale,
                $key,
                array_merge(
                    ['as' => data_get($route, 'action.as')],
                    $options
                )
            )
        ));

        return $this->cleanUniqueRegistrationKey($route, $locale);
    }

    /**
     * Generate a route.
     *
     * @param  string  $key
     * @param  mixed  $handle
     * @param  string  $locale
     * @param  array  $options
     * @return \Illuminate\Routing\Route
     */
    protected function generateRoute(string $key, $handle, string $locale, array $options): Route
    {
        $route = $this->router->addRoute(
            $this->getRequestMethodFromOptions($options),
            $this->applyUniqueRegistrationKey(
                $this->generateUriFromKey($key, $locale),
                $locale
            ),
            $handle ?: '\Illuminate\Routing\ViewController'
        );

        if (is_null($handle)) {
            return $route
                ->defaults('view', Arr::get($options, 'view', $key))
                ->defaults('data', Arr::get($options, 'data', []));
        }

        return $route;
    }

    /**
     * Retrieve the request method from the options.
     *
     * @param  array  $options
     * @return array
     */
    protected function getRequestMethodFromOptions(array $options): array
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
    protected function generateNameForLocaleFromOptions(string $locale, string $key, array $options): string
    {
        $name = Arr::get($options, "names.{$locale}", Arr::get($options, 'name', $key));

        if ($prefix = Arr::get($options, 'as')) {
            return config('laravel-multilingual-routes.name_prefix_before_locale')
                ? "{$prefix}{$locale}.{$name}"
                : "{$locale}.{$prefix}{$name}";
        }

        return "{$locale}.{$name}";
    }

    /**
     * Generate the prefix of the route based on the options.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return string|null
     */
    protected function generatePrefixForLocale(string $key, string $locale): ?string
    {
        if ($key == '/' || $this->shouldNotPrefixLocale($locale)) {
            return null;
        }

        return $locale;
    }

    /**
     * Generate the route uri from the translation key and locale.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return string
     */
    protected function generateUriFromKey(string $key, string $locale): string
    {
        if ($key == '/') {
            return $this->shouldNotPrefixLocale($locale) ? '/' : "/$locale";
        }

        return Lang::has("routes.{$key}")
            ? trans("routes.{$key}", [], $locale)
            : $key;
    }

    /**
     * Apply the unique registration key to make sure the route is registered.
     *
     * @param  string  $uri
     * @param  string  $locale
     * @return string
     */
    protected function applyUniqueRegistrationKey(string $uri, string $locale): string
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
    protected function cleanUniqueRegistrationKey(Route $route, string $locale): Route
    {
        return $route->setUri(str_replace("__{$locale}__", '', $route->uri));
    }

    /**
     * Verify if we should not prefix the locale.
     *
     * @param  string  $locale
     * @return bool
     */
    protected function shouldNotPrefixLocale(string $locale): bool
    {
        return $locale == config('laravel-multilingual-routes.default')
            && ! config('laravel-multilingual-routes.prefix_default');
    }
}
