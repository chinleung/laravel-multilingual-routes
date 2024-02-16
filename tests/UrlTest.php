<?php

namespace ChinLeung\MultilingualRoutes\Tests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Orchestra\Testbench\TestCase;

class UrlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['locales.supported' => [
            'en', 'fr',
        ]]);
    }

    /** @test **/
    public function a_multilingual_route_can_be_signed(): void
    {
        Route::multilingual('test');

        $request = Request::create(URL::signedLocalizedRoute('test'));

        $this->assertTrue(URL::hasValidSignature($request));
        $this->assertTrue(str_starts_with($request->url(), localized_route('test')));
    }

    /** @test **/
    public function a_multilingual_route_can_be_signed_with_temporary_signature(): void
    {
        Route::multilingual('test');

        $request = Request::create(URL::temporarySignedLocalizedRoute('test', now()->addMinutes(30)));

        $this->assertTrue(URL::hasValidSignature($request));
        $this->assertTrue(str_starts_with($request->url(), localized_route('test')));

        $this->travel(5)->hours();

        $this->assertFalse(URL::hasValidSignature($request));
    }
}
