<?php

namespace ChinLeung\LaravelMultilingualRoutes\Tests;

use ChinLeung\LaravelLocales\LaravelLocalesServiceProvider;
use ChinLeung\LaravelMultilingualRoutes\DetectRequestLocale;
use ChinLeung\LaravelMultilingualRoutes\LaravelMultilingualRoutesServiceProvider;
use ChinLeung\LaravelMultilingualRoutes\MultilingualRoutePendingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

class RouteTest extends TestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        config(['locales.supported' => [
            'en', 'fr',
        ]]);
    }

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

    /** @test **/
    public function the_home_page_can_be_registered() : void
    {
        Route::multilingual('/', function () {
            //
        })->name('home');

        $this->assertEquals(config('app.url'), localized_route('home'));
        $this->assertEquals(config('app.url').'/fr', localized_route('home', [], 'fr'));
    }

    /** @test **/
    public function a_route_without_handle_can_be_registered() : void
    {
        $this->registerTestTranslations();

        Route::multilingual('test');

        $this->assertEquals(config('app.url').'/test', localized_route('test'));
        $this->assertEquals(config('app.url').'/fr/teste', localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function a_route_with_identical_keys_can_be_registered() : void
    {
        Route::multilingual('test');

        $this->assertEquals(config('app.url').'/routes.test', localized_route('test'));
        $this->assertEquals(config('app.url').'/fr/routes.test', localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function a_route_with_prefix_stack_can_be_registered() : void
    {
        $this->registerTestTranslations();

        Route::prefix('prefix')->group(function () {
            Route::multilingual('test');
        });

        $this->assertEquals(config('app.url').'/prefix/test', localized_route('test'));
        $this->assertEquals(config('app.url').'/fr/prefix/teste', localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function a_view_route_can_be_registered() : void
    {
        Route::multilingual('/')->view('app')->name('home');

        $this->assertEquals(config('app.url'), localized_route('home'));
        $this->assertEquals(config('app.url').'/fr', localized_route('home', [], 'fr'));
    }

    /** @test **/
    public function a_route_param_can_have_constraints() : void
    {
        $this->registerTranslations([
            'en' => [
                'routes.search' => 'search/{filter?}',
            ],
            'fr' => [
                'routes.search' => 'recherche/{filter?}',
            ],
        ]);

        Route::multilingual('search')->where('filter', '.*')->name('search.results');

        $this->assertEquals(config('app.url').'/search/Foo', localized_route('search.results', ['filter' => 'Foo']));
        $this->assertEquals(config('app.url').'/fr/recherche/Bar', localized_route('search.results', ['filter' => 'Bar'], 'fr'));
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
        $this->registerTranslations([
            'en' => [
                'routes.test' => 'test',
            ],
            'fr' => [
                'routes.test' => 'teste',
            ],
        ]);
    }

    protected function registerTranslations(array $translations) : self
    {
        $translator = app('translator');

        foreach ($translations as $locale => $translation) {
            $translator->addLines($translation, $locale);
        }

        return $this;
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLocalesServiceProvider::class,
            LaravelMultilingualRoutesServiceProvider::class,
        ];
    }
}
