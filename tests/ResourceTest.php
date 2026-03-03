<?php

namespace ChinLeung\MultilingualRoutes\Tests;

use ChinLeung\LaravelLocales\LaravelLocalesServiceProvider;
use ChinLeung\MultilingualRoutes\MultilingualRoutesServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['locales.supported' => [
            'en', 'fr',
        ]]);
    }

    #[Test]
    public function a_multilingual_resource_route_can_be_registered(): void
    {
        $this->registerTestTranslations();

        Route::multilingualResource('photos', 'PhotoController');

        // Check that all resource actions are created for every language
        $expectedRoutes = [
            'index' => ['GET', ''],
            'create' => ['GET', '/create'],
            'store' => ['POST', ''],
            'show' => ['GET', '/{photos}'],
            'edit' => ['GET', '/{photos}/edit'],
            'update' => ['PUT', '/{photos}'],
            'destroy' => ['DELETE', '/{photos}'],
        ];

        foreach (locales() as $locale) {
            foreach ($expectedRoutes as $action => $config) {
                $routeName = "{$locale}.photos.{$action}";
                $this->assertTrue(Route::has($routeName), "Route {$routeName} should exist");

                $route = Route::getRoutes()->getByName($routeName);
                $this->assertNotNull($route, "Route {$routeName} should not be null");

                [$method] = $config;
                $this->assertContains(strtoupper($method), $route->methods, "Route {$routeName} should have {$method} method");
            }
        }
    }

    #[Test]
    public function a_multilingual_resource_can_use_translated_uris(): void
    {
        $this->registerTestTranslations();

        Route::multilingualResource('photos', 'PhotoController');

        // Check English route
        $enIndexRoute = Route::getRoutes()->getByName('en.photos.index');
        $this->assertStringContainsString('photos', $enIndexRoute->uri);

        // Check French route (localized)
        $frIndexRoute = Route::getRoutes()->getByName('fr.photos.index');
        $this->assertStringContainsString('fr/photos', $frIndexRoute->uri);
    }

    #[Test]
    public function a_multilingual_resource_can_be_limited_to_specific_actions(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->only(['index', 'show']);

        foreach (locales() as $locale) {
            // Check that only index and show exist
            $this->assertTrue(Route::has("{$locale}.photos.index"));
            $this->assertTrue(Route::has("{$locale}.photos.show"));

            // Check that the others don't exist
            $this->assertFalse(Route::has("{$locale}.photos.create"));
            $this->assertFalse(Route::has("{$locale}.photos.store"));
            $this->assertFalse(Route::has("{$locale}.photos.edit"));
            $this->assertFalse(Route::has("{$locale}.photos.update"));
            $this->assertFalse(Route::has("{$locale}.photos.destroy"));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_exclude_specific_actions(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->except(['create', 'edit']);

        foreach (locales() as $locale) {
            // Check that create and edit don't exist
            $this->assertFalse(Route::has("{$locale}.photos.create"));
            $this->assertFalse(Route::has("{$locale}.photos.edit"));

            // Check that the others exist
            $this->assertTrue(Route::has("{$locale}.photos.index"));
            $this->assertTrue(Route::has("{$locale}.photos.store"));
            $this->assertTrue(Route::has("{$locale}.photos.show"));
            $this->assertTrue(Route::has("{$locale}.photos.update"));
            $this->assertTrue(Route::has("{$locale}.photos.destroy"));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_have_custom_parameter_names(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->parameters([
            'photos' => 'photo_id',
        ]);

        foreach (locales() as $locale) {
            $showRoute = Route::getRoutes()->getByName("{$locale}.photos.show");
            $this->assertStringContainsString('{photo_id}', $showRoute->uri);

            $editRoute = Route::getRoutes()->getByName("{$locale}.photos.edit");
            $this->assertStringContainsString('{photo_id}', $editRoute->uri);

            $updateRoute = Route::getRoutes()->getByName("{$locale}.photos.update");
            $this->assertStringContainsString('{photo_id}', $updateRoute->uri);

            $destroyRoute = Route::getRoutes()->getByName("{$locale}.photos.destroy");
            $this->assertStringContainsString('{photo_id}', $destroyRoute->uri);
        }
    }

    #[Test]
    public function a_multilingual_resource_can_be_limited_to_specific_locales(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->onlyLocales(['fr']);

        // Check that only French routes exist
        $this->assertTrue(Route::has('fr.photos.index'));
        $this->assertFalse(Route::has('en.photos.index'));
    }

    #[Test]
    public function multilingual_resource_generates_correct_route_uris(): void
    {
        $this->registerTestTranslations();

        Route::multilingualResource('photos', 'PhotoController');

        // Check English route URIs
        $this->assertEquals('photos', Route::getRoutes()->getByName('en.photos.index')->uri);
        $this->assertEquals('photos/create', Route::getRoutes()->getByName('en.photos.create')->uri);
        $this->assertEquals('photos', Route::getRoutes()->getByName('en.photos.store')->uri);
        $this->assertEquals('photos/{photos}', Route::getRoutes()->getByName('en.photos.show')->uri);
        $this->assertEquals('photos/{photos}/edit', Route::getRoutes()->getByName('en.photos.edit')->uri);
        $this->assertEquals('photos/{photos}', Route::getRoutes()->getByName('en.photos.update')->uri);
        $this->assertEquals('photos/{photos}', Route::getRoutes()->getByName('en.photos.destroy')->uri);

        // Check French route URIs (with prefix)
        $this->assertEquals('fr/photos', Route::getRoutes()->getByName('fr.photos.index')->uri);
        $this->assertEquals('fr/photos/create', Route::getRoutes()->getByName('fr.photos.create')->uri);
        $this->assertEquals('fr/photos', Route::getRoutes()->getByName('fr.photos.store')->uri);
        $this->assertEquals('fr/photos/{photos}', Route::getRoutes()->getByName('fr.photos.show')->uri);
        $this->assertEquals('fr/photos/{photos}/edit', Route::getRoutes()->getByName('fr.photos.edit')->uri);
        $this->assertEquals('fr/photos/{photos}', Route::getRoutes()->getByName('fr.photos.update')->uri);
        $this->assertEquals('fr/photos/{photos}', Route::getRoutes()->getByName('fr.photos.destroy')->uri);
    }

    protected function registerTestTranslations(): void
    {
        $this->registerTranslations([
            'en' => [
                'routes.photos' => 'photos',
            ],
            'fr' => [
                'routes.photos' => 'photos', // Could be 'photos' or another translation
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

    #[Test]
    public function a_multilingual_resource_can_use_name_method(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->name('gallery');

        foreach (locales() as $locale) {
            $this->assertTrue(Route::has("{$locale}.gallery.index"));
            $this->assertTrue(Route::has("{$locale}.gallery.show"));
            $this->assertTrue(Route::has("{$locale}.gallery.create"));

            // Original name should not exist
            $this->assertFalse(Route::has("{$locale}.photos.index"));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_use_middleware(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->middleware('auth');

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.photos.index");
            $this->assertContains('auth', data_get($route, 'action.middleware', []));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_use_where_constraints(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->where('photos', '[0-9]+');

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.photos.show");
            $this->assertEquals('[0-9]+', data_get($route, 'wheres.photos'));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_use_defaults(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->defaults(['format' => 'json']);

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.photos.index");
            $this->assertEquals('json', data_get($route, 'defaults.format'));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_exclude_locales(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->exceptLocales(['en']);

        // Check that only French routes exist
        $this->assertTrue(Route::has('fr.photos.index'));
        $this->assertFalse(Route::has('en.photos.index'));
    }

    #[Test]
    public function a_multilingual_resource_can_use_names_array(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->names([
            'en' => 'pictures',
            'fr' => 'images',
        ]);

        $this->assertTrue(Route::has('en.pictures.index'));
        $this->assertTrue(Route::has('fr.images.index'));

        // Original names should not exist
        $this->assertFalse(Route::has('en.photos.index'));
        $this->assertFalse(Route::has('fr.photos.index'));
    }

    #[Test]
    public function a_multilingual_resource_can_use_missing_callback(): void
    {
        $callbackExecuted = false;

        Route::multilingualResource('photos', 'PhotoController')->missing(function () use (&$callbackExecuted) {
            $callbackExecuted = true;

            return response('Custom 404', 404);
        });

        // Check that routes exist with the missing callback option
        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.photos.show");
            $this->assertNotNull(data_get($route, 'action.missing'));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_use_with_trashed(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->withTrashed(['show', 'edit']);

        foreach (locales() as $locale) {
            $showRoute = Route::getRoutes()->getByName("{$locale}.photos.show");
            $editRoute = Route::getRoutes()->getByName("{$locale}.photos.edit");

            // These routes should have withTrashed enabled
            $this->assertTrue(data_get($showRoute, 'action.withTrashed', false));
            $this->assertTrue(data_get($editRoute, 'action.withTrashed', false));

            // Index route should not have withTrashed
            $indexRoute = Route::getRoutes()->getByName("{$locale}.photos.index");
            $this->assertFalse(data_get($indexRoute, 'action.withTrashed', false));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_use_where_param(): void
    {
        Route::multilingualResource('photos', 'PhotoController')->whereParam('photos', '[0-9]+');

        foreach (locales() as $locale) {
            $route = Route::getRoutes()->getByName("{$locale}.photos.show");
            $this->assertEquals('[0-9]+', data_get($route, 'wheres.photos'));
        }
    }

    #[Test]
    public function a_multilingual_resource_can_chain_multiple_methods(): void
    {
        Route::multilingualResource('photos', 'PhotoController')
            ->only(['index', 'show', 'edit'])
            ->exceptLocales(['en'])
            ->name('gallery')
            ->middleware('auth')
            ->where('photos', '[0-9]+')
            ->parameters(['photos' => 'photo_id'])
            ->withTrashed(['show']);

        // Check that only French routes exist
        $this->assertTrue(Route::has('fr.gallery.index'));
        $this->assertTrue(Route::has('fr.gallery.show'));
        $this->assertTrue(Route::has('fr.gallery.edit'));
        $this->assertFalse(Route::has('en.gallery.index'));

        // Check that excluded actions don't exist
        $this->assertFalse(Route::has('fr.gallery.create'));
        $this->assertFalse(Route::has('fr.gallery.store'));

        // Check parameter name
        $showRoute = Route::getRoutes()->getByName('fr.gallery.show');
        $this->assertStringContainsString('{photo_id}', $showRoute->uri);

        // Check constraints
        $this->assertEquals('[0-9]+', data_get($showRoute, 'wheres.photos'));

        // Check middleware
        $this->assertContains('auth', data_get($showRoute, 'action.middleware', []));

        // Check withTrashed
        $this->assertTrue(data_get($showRoute, 'action.withTrashed', false));
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLocalesServiceProvider::class,
            MultilingualRoutesServiceProvider::class,
        ];
    }
}
