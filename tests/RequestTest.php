<?php

namespace ChinLeung\LaravelMultilingualRoutes\Tests;

use ChinLeung\LaravelLocales\LaravelLocalesServiceProvider;
use ChinLeung\LaravelMultilingualRoutes\LaravelMultilingualRoutesServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class RequestTest extends TestCase
{
    /** @test **/
    public function a_request_name_can_be_matched(): void
    {
        Route::multilingual('test');

        $request = Request::create(localized_route('test'));

        $request->setRouteResolver(function () {
            return Route::getRoutes()->getByName('en.test');
        });

        $this->assertFalse($request->routeIs('test'));
        $this->assertTrue($request->routeIs('en.test'));
        $this->assertTrue($request->localizedRouteIs('test'));
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLocalesServiceProvider::class,
            LaravelMultilingualRoutesServiceProvider::class,
        ];
    }
}
