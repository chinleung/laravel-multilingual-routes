<?php

namespace ChinLeung\MultilingualRoutes\Tests;

use ChinLeung\LaravelLocales\LaravelLocalesServiceProvider;
use ChinLeung\MultilingualRoutes\MultilingualRoutesServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UnprefixedRedirectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'locales.supported' => ['en', 'fr'],
            'laravel-multilingual-routes.default' => 'en',
            'laravel-multilingual-routes.prefix_default' => true,
            'laravel-multilingual-routes.redirect_unprefixed' => true,
        ]);
    }

    #[Test]
    public function an_unprefixed_localized_route_redirects_to_the_default_locale(): void
    {
        Route::multilingual('posts/{post}', static function () {
            //
        })->where('post', '[0-9]+');

        $this->get('/posts/123')->assertRedirect('/en/posts/123');
        $this->get('/posts/not-a-number')->assertNotFound();
    }

    #[Test]
    public function get_parameters_are_maintained_on_redirect(): void
    {
        Route::multilingual('search');

        $response = $this->get('/search?query=multilingual%20routes&page=2');
        $location = $response->headers->get('Location');
        parse_str(parse_url($location, PHP_URL_QUERY), $query);

        $this->assertSame('/en/search', parse_url($location, PHP_URL_PATH));
        $this->assertSame('multilingual routes', $query['query']);
        $this->assertSame('2', $query['page']);
    }

    #[Test]
    public function a_url_without_an_applicable_default_locale_route_is_not_redirected(): void
    {
        Route::multilingual('french-only')->only('fr');

        $this->get('/french-only')->assertNotFound();
        $this->get('/not-localized')->assertNotFound();
    }

    #[Test]
    public function an_unprefixed_default_locale_does_not_create_a_redirect(): void
    {
        config(['laravel-multilingual-routes.prefix_default' => false]);

        Route::multilingual('posts', static fn () => 'posts');

        $this->get('/posts')->assertOk()->assertSee('posts');
    }

    #[Test]
    public function a_non_get_route_does_not_create_an_unprefixed_redirect(): void
    {
        Route::multilingual('posts', static function () {
            //
        })->method('post');

        $this->post('/posts')->assertNotFound();
        $this->post('/en/posts')->assertOk();
    }

    #[Test]
    public function the_home_route_redirects_when_the_default_home_is_prefixed(): void
    {
        config(['laravel-multilingual-routes.prefix_default_home' => true]);

        Route::multilingual('/');

        $this->get('/')->assertRedirect('/en');
    }

    #[Test]
    public function an_unprefixed_default_home_route_is_left_untouched(): void
    {
        config(['laravel-multilingual-routes.prefix_default_home' => false]);

        Route::multilingual('/', static fn () => 'home');

        $this->get('/')->assertOk()->assertSee('home');
    }

    #[Test]
    public function an_unprefixed_redirect_respects_route_groups(): void
    {
        Route::prefix('admin')->group(static function () {
            Route::multilingual('posts');
        });

        $response = $this->get('/admin/posts');

        $response->assertRedirect('/en/admin/posts');
        $this->assertSame('/en/admin/posts', $response->headers->get('Location'));
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLocalesServiceProvider::class,
            MultilingualRoutesServiceProvider::class,
        ];
    }
}
