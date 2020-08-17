<?php

namespace ChinLeung\MultilingualRoutes\Tests;

use ChinLeung\LaravelLocales\LaravelLocalesServiceProvider;
use ChinLeung\MultilingualRoutes\DetectRequestLocale;
use ChinLeung\MultilingualRoutes\MultilingualRoutePendingRegistration;
use ChinLeung\MultilingualRoutes\MultilingualRoutesServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;

class RouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['locales.supported' => [
            'en', 'fr',
        ]]);
    }

    /** @test **/
    public function a_multilingual_route_can_be_registered(): void
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
    public function a_route_can_have_different_names_based_on_locales(): void
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
    public function the_group_name_can_be_renamed(): void
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
    public function the_locale_name_has_priority_over_group_name(): void
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
    public function it_can_limit_route_to_specific_locales(): void
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
    public function it_can_remove_specific_locales_from_route(): void
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
    public function the_default_locale_routes_can_be_prefixed(): void
    {
        config(['laravel-multilingual-routes.prefix_default' => true]);

        $this->registerTestRoute();

        $this->assertEquals(
            route('fr.test'),
            $route = localized_route('test', [], 'fr')
        );

        $this->assertRegexp('/fr/', $route);
    }

    /** @test **/
    public function it_can_register_a_post_route(): void
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
    public function the_app_locale_will_be_used_in_case_of_wrong_locale(): void
    {
        $this->registerTestRoute();

        $this->assertEquals(
            route(app()->getLocale().'.test'),
            localized_route('test', [], 'cz')
        );
    }

    /** @test **/
    public function the_request_locale_can_be_changed_by_the_middleware(): void
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
    public function the_home_page_can_be_registered(): void
    {
        Route::multilingual('/', static function () {
            //
        })->name('home');

        $this->assertEquals(url(''), localized_route('home'));
        $this->assertEquals(url('fr'), localized_route('home', [], 'fr'));
    }

    /** @test **/
    public function a_route_without_handle_can_be_registered(): void
    {
        $this->registerTestTranslations();

        Route::multilingual('test');

        $this->assertEquals(url('test'), localized_route('test'));
        $this->assertEquals(url('fr/teste'), localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function a_route_with_identical_keys_can_be_registered(): void
    {
        Route::multilingual('test');

        $this->assertEquals(url('test'), localized_route('test'));
        $this->assertEquals(url('fr/test'), localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function a_route_with_prefix_stack_can_be_registered(): void
    {
        $this->registerTestTranslations();

        Route::prefix('prefix')->group(static function () {
            Route::multilingual('test');
        });

        $this->assertEquals(url('prefix/test'), localized_route('test'));
        $this->assertEquals(url('fr/prefix/teste'), localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function a_view_route_can_be_registered(): void
    {
        Route::multilingual('/')->view('app')->name('home');

        $this->assertEquals(config('app.url'), localized_route('home'));
        $this->assertEquals(url('fr'), localized_route('home', [], 'fr'));
    }

    /** @test **/
    public function a_view_route_can_be_registered_with_custom_data(): void
    {
        Route::multilingual('/')->name('home')->view('app', [
            'name' => 'Taylor',
        ]);

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.home");

            $this->assertEquals(
                'Taylor',
                Arr::get($route->defaults, 'data.name')
            );
        }
    }

    /** @test **/
    public function a_view_route_can_be_registered_with_custom_data_via_method(): void
    {
        Route::multilingual('/')->name('home')->view('app')->data([
            'name' => 'Taylor',
        ]);

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.home");

            $this->assertEquals(
                'Taylor',
                Arr::get($route->defaults, 'data.name')
            );
        }
    }

    /** @test **/
    public function a_route_param_can_have_constraints(): void
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

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.search.results");

            $this->assertEquals('.*', Arr::get($route->wheres, 'filter'));
        }
    }

    /** @test **/
    public function a_route_param_can_have_constraints_by_locale(): void
    {
        $this->registerTranslations([
            'en' => [
                'routes.search' => 'search/{filter}/{filter2}',
            ],
            'fr' => [
                'routes.search' => 'recherche/{filter}/{filter2}',
            ],
        ]);

        Route::multilingual('search')->where('filter', '.*')
            ->where('filter2', 'fr', 'fr')
            ->where('filter2', 'en', 'en')
            ->name('search.results');

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.search.results");

            $this->assertEquals('.*', Arr::get($route->wheres, 'filter'));
            $this->assertEquals($locale, Arr::get($route->wheres, 'filter2'));
        }
    }

    /** @test **/
    public function a_route_without_translation_will_be_registered_with_its_key(): void
    {
        Route::multilingual('test');

        $this->assertEquals(url('test'), localized_route('test'));
        $this->assertEquals(url('fr/test'), localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function a_starting_slash_will_be_trimmed_from_translation(): void
    {
        $this->registerTestTranslations();

        Route::multilingual('/test', static function () {
            //
        });

        $this->assertEquals(url('test'), localized_route('test'));
        $this->assertEquals(url('fr/teste'), localized_route('test', [], 'fr'));
    }

    /** @test **/
    public function the_current_route_can_be_retrieved_in_a_different_locale(): void
    {
        $this->registerTestRoute();

        Route::dispatch(Request::create(localized_route('test')));

        $this->assertEquals(localized_route('test', [], 'fr'), current_route('fr'));
    }

    /** @test **/
    public function the_current_route_can_be_retrieved_in_a_different_locale_with_query_strings(): void
    {
        $this->registerTestRoute();

        app()->bind('request', static function () {
            return Request::create(localized_route('test'), 'GET', [
                'foo' => 'bar',
            ]);
        });

        Route::dispatch(request());

        $this->assertEquals(
            localized_route('test', ['foo' => 'bar'], 'fr'),
            current_route('fr')
        );
    }

    /** @test **/
    public function the_current_route_will_fallback_to_current_route_by_default(): void
    {
        Route::view('test', 'app');

        app()->bind('request', static function () {
            return Request::create(url('test'), 'GET');
        });

        Route::dispatch(request());

        $this->assertEquals(url('test'), current_route('fr'));
    }

    /** @test **/
    public function the_current_route_can_have_a_custom_fallback(): void
    {
        Route::view('test', 'app');
        Route::view('fallback', 'app');

        app()->bind('request', static function () {
            return Request::create(url('test'), 'GET');
        });

        Route::dispatch(request());

        $this->assertEquals(
            url('fallback'),
            current_route('fr', url('fallback'))
        );
    }

    /** @test **/
    public function a_route_prefix_can_be_registered_after_the_locale(): void
    {
        Route::name('prefix.')->group(static function () {
            Route::multilingual('test');
        });

        $this->assertNotNull(localized_route('prefix.test'));
        $this->assertNotNull(localized_route('prefix.test', [], 'fr'));
    }

    /** @test **/
    public function a_route_prefix_can_be_registered_before_the_locale(): void
    {
        config([
            'laravel-multilingual-routes.name_prefix_before_locale' => true,
        ]);

        Route::name('prefix.')->group(static function () {
            Route::multilingual('test');
        });

        $this->assertNotNull(route('prefix.en.test'));
        $this->assertNotNull(route('prefix.fr.test'));
    }

    /** @test **/
    public function a_route_with_defaults_parameters_can_be_registered(): void
    {
        $params = ['param_1' => 'value_1', 'param_2' => 'value_2'];
        Route::multilingual('test')->defaults($params)->name('test');

        foreach (config('locales.supported') as $locale) {
            $route = Route::getRoutes()->getByName($locale.'.test');

            foreach ($params as $key => $value) {
                $this->assertArrayHasKey($key, $route->defaults);
                $this->assertEquals($value, $route->defaults[$key]);
            }
        }
    }

    /** @test **/
    public function the_default_home_page_can_be_registered_with_prefix(): void
    {
        config([
            'laravel-multilingual-routes.prefix_default' => true,
            'laravel-multilingual-routes.prefix_default_home' => true,
        ]);

        Route::multilingual('/', static function () {
            //
        })->name('home');

        $this->assertEquals(url('en'), localized_route('home'));
        $this->assertEquals(url('fr'), localized_route('home', [], 'fr'));
    }

    /** @test **/
    public function the_default_home_page_can_be_registered_without_prefix(): void
    {
        config([
            'laravel-multilingual-routes.prefix_default' => true,
            'laravel-multilingual-routes.prefix_default_home' => false,
        ]);

        Route::multilingual('/', static function () {
            //
        })->name('home');

        $this->assertEquals(url(''), localized_route('home'));
        $this->assertEquals(url('fr'), localized_route('home', [], 'fr'));
    }

    /** @test **/
    public function a_route_can_be_registered_with_a_middleware(): void
    {
        Route::multilingual('/')->middleware('web');

        foreach (Route::getRoutes() as $route) {
            $this->assertContains('web', data_get($route, 'action.middleware'));
        }
    }

    protected function registerTestRoute(): MultilingualRoutePendingRegistration
    {
        $this->registerTestTranslations();

        return Route::multilingual(
            'test',
            static function () {
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

    protected function registerTranslations(array $translations): self
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
            MultilingualRoutesServiceProvider::class,
        ];
    }
}
