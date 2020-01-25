<?php

namespace ChinLeung\MultilingualRoutes\Tests;

use ChinLeung\LaravelLocales\LaravelLocalesServiceProvider;
use ChinLeung\MultilingualRoutes\MultilingualRoutesServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class RedirectTest extends TestCase
{
    /** @test **/
    public function a_localized_redirect_can_be_made(): void
    {
        Route::multilingual('start', function () {
            return redirect()->localizedRoute('destination');
        });

        Route::multilingual('destination');

        $response = $this->get(localized_route('start'));

        $response->assertRedirect(localized_route('destination'));
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLocalesServiceProvider::class,
            MultilingualRoutesServiceProvider::class,
        ];
    }
}
