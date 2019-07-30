<?php

namespace ChinLeung\LaravelMultilingualRoutes\Tests;

use ChinLeung\LaravelMultilingualRoutes\DetectRequestLocale;
use ChinLeung\LaravelMultilingualRoutes\LaravelMultilingualRoutesServiceProvider;
use ChinLeung\LaravelMultilingualRoutes\MultilingualRoutePendingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use ChinLeung\LaravelLocales\LaravelLocalesServiceProvider;

class RouteTest extends TestCase
{
    /** @test **/
    public function a_multilingual_route_can_be_registered() : void
    {
        $this->registerTestRoute();

        foreach (locales() as $locale) {
            $this->assertEquals(
                route($locale.'.test'),
                localized_route('test', [], $locale)
            );
        }
    }

    /** @test **/
    public function a_route_can_have_different_names_based_on_locales() : void
    {
        $this
            ->registerTestRoute()
            ->names([
                'en' => 'testing',
                'fr' => 'teste',
            ]);

        $this->assertEquals(
            route('en.testing'),
            localized_route('testing', [], 'en')
        );

        $this->assertEquals(
            route('fr.teste'),
            localized_route('teste', [], 'fr')
        );
    }

    /** @test **/
    public function the_group_name_can_be_renamed() : void
    {
        $this
            ->registerTestRoute()
            ->name('foo');

        foreach (locales() as $locale) {
            $this->assertEquals(
                route($locale.'.foo'),
                localized_route('foo', [], $locale)
            );
        }
    }

    /** @test **/
    public function the_locale_name_has_priority_over_group_name() : void
    {
        $this
            ->registerTestRoute()
            ->name('foo')
            ->names([
                'fr' => 'bar',
            ]);

        $this->assertEquals(
            route('en.foo'),
            localized_route('foo', [], 'en')
        );

        $this->assertEquals(
            route('fr.bar'),
            localized_route('bar', [], 'fr')
        );

        $this->expectException(InvalidArgumentException::class);
        localized_route('foo', [], 'fr');
    }

    /** @test **/
    public function it_can_limit_route_to_specific_locales() : void
    {
        $this->registerTestRoute()
             ->only(['fr']);

        $this->assertEquals(
            route('fr.test'),
            localized_route('test', [], 'fr')
        );

        $this->expectException(InvalidArgumentException::class);
        localized_route('test', [], 'en');
    }

    /** @test **/
    public function it_can_remove_specific_locales_from_route() : void
    {
        $this->registerTestRoute()
             ->except(['fr']);

        $this->assertEquals(
            route('en.test'),
            localized_route('test', [], 'en')
        );

        $this->expectException(InvalidArgumentException::class);
        localized_route('test', [], 'fr');
    }

    /** @test **/
    public function the_fallback_locale_routes_can_be_prefixed() : void
    {
        config(['laravel-multilingual-routes.prefix_fallback' => true]);

        $this->registerTestRoute();

        $this->assertEquals(
            route('fr.test'),
            $route = localized_route('test', [], 'fr')
        );

        $this->assertRegexp('/fr/', $route);
    }

    /** @test **/
    public function it_can_register_a_post_route() : void
    {
        $routes = $this
            ->registerTestRoute()
            ->method('post')
            ->register();

        foreach ($routes as $route) {
            $this->assertContains(
                'POST',
                $route->methods
            );
        }
    }

    /** @test **/
    public function the_app_locale_will_be_used_in_case_of_wrong_locale() : void
    {
        $this->registerTestRoute();

        $this->assertEquals(
            route(app()->getLocale().'.test'),
            localized_route('test', [], 'cz')
        );
    }

    /** @test **/
    public function the_request_locale_can_be_changed_by_the_middleware() : void
    {
        $this->registerTestRoute();

        (new DetectRequestLocale)->handle(
            Request::create(localized_route('test', [], 'fr')),
            function () {
                $this->assertEquals(
                    'fr',
                    app()->getLocale()
                );
            }
        );
    }

    protected function registerTestRoute() : MultilingualRoutePendingRegistration
    {
        $this->registerTestTranslations();

        return Route::multilingual(
            'test',
            function () {
                //
            }
        );
    }

    protected function registerTestTranslations()
    {
        $translator = app('translator');

        $translator->addLines(
            ['routes.test' => 'test'],
            'en'
        );

        $translator->addLines(
            ['routes.test' => 'teste'],
            'fr'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLocalesServiceProvider::class,
            LaravelMultilingualRoutesServiceProvider::class,
        ];
    }
}
