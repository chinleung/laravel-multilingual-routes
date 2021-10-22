<?php

namespace ChinLeung\MultilingualRoutes;

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
    protected string $key;

    /**
     * The list of locales for the route.
     *
     * @var array
     */
    protected array $locales = [];

    /**
     * The options of the routes.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * The resource's registration status.
     *
     * @var bool
     */
    protected bool $registered = false;

    /**
     * The resource registrar.
     *
     * @var \ChinLeung\MultilingualRoutes\MultilingualRegistrar
     */
    protected MultilingualRegistrar $registrar;

    /**
     * If the registered route is a redirect.
     *
     * @var bool
     */
    protected bool $isRedirect = false;

    /**
     * Redirect destination.
     *
     * @var string|null
     */
    protected ?string $destination = null;

    /**
     * Redirect status.
     *
     * @var int|null
     */
    protected ?int $status = null;

    /**
     * Constructor of the class.
     *
     * @param  \ChinLeung\MultilingualRoutes\MultilingualRegistrar  $registrar
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

        if (! $this->isRedirect) {
            return $this->registrar->register(
                $this->key,
                $this->handle,
                $this->options['locales'] ?? $this->locales,
                $this->options
            );
        }

        return $this->registrar->redirect(
            $this->key,
            $this->destination,
            $this->status,
            $this->options['locales'] ?? $this->locales,
            $this->options
        );
    }

    /**
     * Set the default view data of the route.
     *
     * @param  array  $data
     * @return self
     */
    public function data(array $data): self
    {
        $this->options['data'] = $data;

        return $this;
    }

    /**
     * Add one or many locale to the exception.
     *
     * @param  string|array  $locales
     * @return self
     */
    public function except($locales): self
    {
        $this->options['locales'] = array_diff($this->locales, Arr::wrap($locales));

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
     * Set the middleware of the routes.
     *
     * @param  string|array  $middleware
     * @return self
     */
    public function middleware($middleware): self
    {
        $this->options['middleware'] = $middleware;

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
        $this->options['locales'] = array_intersect($this->locales, Arr::wrap($locales));

        return $this;
    }

    /**
     * Set a regular expression requirement on the route.
     *
     * @param  array|string  $name
     * @param  string|null  $expression
     * @param  string|null  $locale
     * @return $this
     */
    public function where($name, ?string $expression = null, string $locale = null): self
    {
        $key = rtrim("constraints-$locale", '-');

        if (! is_array(Arr::get($this->options, $key))) {
            Arr::set($this->options, $key, []);
        }

        Arr::set($this->options, "$key.$name", $expression);

        return $this;
    }

    /**
     * Set the view to render.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return self
     */
    public function view(string $view, array $data = [], int $status = 200, array $headers = []): self
    {
        $this->options['view'] = $view;

        if (filled($data)) {
            $this->options['data'] = $data;
        }

        $this->options['status'] = $status;

        if (filled($headers)) {
            $this->options['headers'] = $headers;
        }

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
     * Create a redirect from one URI to another.
     *
     * @param  mixed  $destination
     * @param  int  $status
     * @return $this
     */
    public function redirect($destination, int $status = 302): self
    {
        $this->isRedirect = true;
        $this->destination = $destination;
        $this->status = $status;

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
