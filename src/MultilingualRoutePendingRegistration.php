<?php

namespace ChinLeung\LaravelMultilingualRoutes;

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Arr;

class MultilingualRoutePendingRegistration
{
    /**
     * The handle of the routes.
     *
     * @var mixed
     */
    protected $handle;

    /**
     * The translation key of the routes.
     *
     * @var string
     */
    protected $key;

    /**
     * The list of locales for the route.
     *
     * @var array
     */
    protected $locales = [];

    /**
     * The options of the routes.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The resource's registration status.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * The resource registrar.
     *
     * @var \ChinLeung\LaravelMultilingualRoutes\MultilingualRegistrar
     */
    protected $registrar;

    /**
     * Constructor of the class.
     *
     * @param  \ChinLeung\LaravelMultilingualRoutes\MultilingualRegistrar  $registrar
     * @param  string  $key
     * @param  mixed  $handle
     * @param  array  $locales
     */
    public function __construct(MultilingualRegistrar $registrar, string $key, $handle, array $locales = [])
    {
        $this->key = $key;
        $this->registrar = $registrar;
        $this->handle = $handle;
        $this->locales = $locales;
    }

    /**
     * Register the resource route.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register(): RouteCollection
    {
        $this->registered = true;

        return $this->registrar->register(
            $this->key,
            $this->handle,
            $this->options['locales'] ?? $this->locales,
            $this->options
        );
    }

    /**
     * Add one or many locale to the exception.
     *
     * @param  string|array  $locales
     * @return self
     */
    public function except($locales): self
    {
        $this->options['locales'] = array_diff(
            $this->locales,
            Arr::wrap($locales)
        );

        return $this;
    }

    /**
     * Set the name of the routes.
     *
     * @param  string  $name
     * @return self
     */
    public function name(string $name): self
    {
        $this->options['name'] = $name;

        return $this;
    }

    /**
     * Set the method of the routes.
     *
     * @param  string  $method
     * @return self
     */
    public function method(string $method): self
    {
        $this->options['method'] = $method;

        return $this;
    }

    /**
     * Set the name of each locale for the routes.
     *
     * @param  array  $names
     * @return self
     */
    public function names(array $names): self
    {
        $this->options['names'] = $names;

        return $this;
    }

    /**
     * Set the route for a list of locales only.
     *
     * @param  string|array  $locales
     * @return self
     */
    public function only($locales): self
    {
        $this->options['locales'] = array_intersect(
            $this->locales,
            Arr::wrap($locales)
        );

        return $this;
    }

    /**
     * Set a regular expression requirement on the route.
     *
     * @param  array|string  $name
     * @param  string|null  $expression
     * @return $this
     */
    public function where($name, $expression = null): self
    {
        if (! is_array(Arr::get($this->options, 'constraints'))) {
            Arr::set($this->options, 'constraints', []);
        }

        Arr::set($this->options, "constraints.$name", $expression);

        return $this;
    }

    /**
     * Set the view to render.
     *
     * @param  string  $view
     * @return self
     */
    public function view(string $view): self
    {
        $this->options['view'] = $view;

        return $this;
    }

    /**
     * Set default parameters values of the routes.
     *
     * @param  array  $defaults
     * @return self
     */
    public function defaults(array $defaults): self
    {
        $this->options['defaults'] = $defaults;

        return $this;
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}
