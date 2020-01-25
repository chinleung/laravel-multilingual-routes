<?php

namespace ChinLeung\MultilingualRoutes;

use ChinLeung\MultilingualRoutes\Macros\RedirectorMacros;
use ChinLeung\MultilingualRoutes\Macros\RequestMacros;
use ChinLeung\MultilingualRoutes\Macros\RouterMacros;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\ServiceProvider;

class MultilingualRoutesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Redirect::mixin(new RedirectorMacros);
        Request::mixin(new RequestMacros);
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
