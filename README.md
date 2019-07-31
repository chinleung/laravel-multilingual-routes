# Laravel Multilingual Routes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chinleung/laravel-multilingual-routes.svg?style=flat-square)](https://packagist.org/packages/chinleung/laravel-multilingual-routes)
[![Build Status](https://img.shields.io/travis/chinleung/laravel-multilingual-routes/master.svg?style=flat-square)](https://travis-ci.org/chinleung/laravel-multilingual-routes)
[![Quality Score](https://img.shields.io/scrutinizer/g/chinleung/laravel-multilingual-routes.svg?style=flat-square)](https://scrutinizer-ci.com/g/chinleung/laravel-multilingual-routes)
[![Total Downloads](https://img.shields.io/packagist/dt/chinleung/laravel-multilingual-routes.svg?style=flat-square)](https://packagist.org/packages/chinleung/laravel-multilingual-routes)

A package to register multilingual routes for your application.

## Installation

You can install the package via composer:

```bash
composer require chinleung/laravel-multilingual-routes
```

To detect and change the locale of the application based on the request automatically, you can add the middleware to your `app/Http/Kernel`:

``` php
protected $middlewareGroups = [
    'web' => [
        \ChinLeung\LaravelMultilingualRoutes\DetectRequestLocale::class,
        // ...
    ]
];
```

## Configuration

By default, the application locales are going to be `en` and `fr` and the `app.fallback_locale` is not prefixed. If you want to prefix the fallback locale, please run the following command to publish the configuration file:

``` bash
php artisan vendor:publish --provider="ChinLeung\LaravelMultilingualRoutes\LaravelMultilingualRoutesServiceProvider" --tag="config"
```

If your application supports different locales, you can either set a `app.locales` configuration or follow the configuration instructions from [chinleung/laravel-locales](https://github.com/chinleung/laravel-locales#configuration).

## Usage

### Quick Usage

Once you have configured the locales, you can start adding routes like the following example in your `routes/web.php`:

``` php
Route::multilingual('test', 'TestController');
```

This will generate the following:

| Method   | URI     | Name    | Action                              |
|----------|---------|---------|-------------------------------------|
| GET\|HEAD | en/test | en.test | App\Http\Controllers\TestController |
| GET\|HEAD | teste   | fr.test | App\Http\Controllers\TestController |

Note the `URI` column is generated from a translation file located at `resources/lang/{locale}/routes.php` which contains the key of the route like the following:

``` php
<?php

// resources/lang/fr/routes.php

return [
  'test' => 'teste'
];
```

To retrieve a route, you can use the `localized_route(string $name, array $parameters, string $locale = null, bool $absolute = true)` instead of the `route` helper:

``` php
localized_route('test'); // Returns the url of the current application locale
localized_route('test', [], 'fr'); // Returns https://app.test/teste
localized_route('test', [], 'en'); // Returns https://app.test/en/test
```

### Renaming the routes

``` php
Route::multilingual('test', 'TestController')->name('foo');
```

| Method   | URI     | Name   | Action                              |
|----------|---------|--------|-------------------------------------|
| GET\|HEAD | en/test | en.foo | App\Http\Controllers\TestController |
| GET\|HEAD | teste   | fr.foo | App\Http\Controllers\TestController |

### Renaming a route based on the locale

``` php
Route::multilingual('test', 'TestController')->names([
  'en' => 'foo',
  'fr' => 'bar',
]);
```

| Method   | URI     | Name   | Action                              |
|----------|---------|--------|-------------------------------------|
| GET\|HEAD | en/test | en.foo | App\Http\Controllers\TestController |
| GET\|HEAD | teste   | fr.bar | App\Http\Controllers\TestController |

### Skipping a locale

``` php
Route::multilingual('test', 'TestController')->except(['fr']);
```

| Method   | URI     | Name    | Action                              |
|----------|---------|---------|-------------------------------------|
| GET\|HEAD | en/test | en.test | App\Http\Controllers\TestController |

### Restricting to a list of locales

``` php
Route::multilingual('test', 'TestController')->only(['fr']);
```


| Method   | URI     | Name    | Action                              |
|----------|---------|---------|-------------------------------------|
| GET\|HEAD | teste   | fr.test | App\Http\Controllers\TestController |

### Changing the method of the request

``` php
Route::multilingual('test', 'TestController')->method('post');
```

| Method | URI     | Name    | Action                              |
|--------|---------|---------|-------------------------------------|
| POST   | en/test | en.test | App\Http\Controllers\TestController |
| POST   | teste   | fr.test | App\Http\Controllers\TestController |

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email chinleung94@gmail.com instead of using the issue tracker.

## Credits

- [Chin Leung](https://github.com/chinleung)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
