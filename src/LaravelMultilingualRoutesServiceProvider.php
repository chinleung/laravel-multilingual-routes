<?php

namespace Chinleung\LaravelMultilingualRoutes;

use Chinleung\LaravelMultilingualRoutes\Macros\RouterMacros;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelMultilingualRoutesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Router::mixin(new RouterMacros);

        require __DIR__.'/helpers.php';

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-multilingual-routes.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-multilingual-routes');
    }
}
