# Changelog

All notable changes to `laravel-multilingual-routes` will be documented in this file.

## [v2.4.0 (2020-06-11)](https://github.com/chinleung/laravel-multilingual-routes/compare/v2.3.0...v2.4.0)

- Added fallback to `current_route` helper [#36](https://github.com/chinleung/laravel-multilingual-routes/issues/36)

## [v2.3.0 (2020-04-10)](https://github.com/chinleung/laravel-multilingual-routes/compare/v2.2.0...v2.3.0)

- Fixed `current_route` helper not working properly for routes with parameters [#31](https://github.com/chinleung/laravel-multilingual-routes/pull/31)
- Added `defaults` method when registering route [#32](https://github.com/chinleung/laravel-multilingual-routes/pull/32)
- Added option `MULTILINGUAL_ROUTES_PREFIX_DEFAULT_HOME` to prefix home route [#33](https://github.com/chinleung/laravel-multilingual-routes/pull/33)

## [v2.2.0 (2020-03-03)](https://github.com/chinleung/laravel-multilingual-routes/compare/v2.1.0...v2.2.0)

- Added support for Laravel 7

## [v2.1.0 (2020-02-04)](https://github.com/chinleung/laravel-multilingual-routes/compare/v2.0.1...v2.1.0)

- Added option `MULTILINGUAL_ROUTES_NAME_PREFIX_BEFORE_LOCALE` to manage name prefix of routes [#30](https://github.com/chinleung/laravel-multilingual-routes/issues/30)

## [v2.0.1 (2020-01-28)](https://github.com/chinleung/laravel-multilingual-routes/compare/v2.0.0...v2.0.1)

- Fixed homepage returning 404 [#28](https://github.com/chinleung/laravel-multilingual-routes/issues/28)

## [v2.0.0 (2020-01-24)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.5.0...v2.0.0)

- Rename the namespace from `ChinLeung\LaravelMultilingualRoutes` to `ChinLeung\MultilingualRoutes` [#26](https://github.com/chinleung/laravel-multilingual-routes/issues/26)

## [v1.5.0 (2020-01-24)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.4.3...v1.5.0)

- Trim starting slash from key [#21](https://github.com/chinleung/laravel-multilingual-routes/issues/21)
- Added `isLocalizedRoute` macro to `Illuminate\Http\Request` [#22](https://github.com/chinleung/laravel-multilingual-routes/issues/22)
- Improved the default behaviour for keys without translations [#23](https://github.com/chinleung/laravel-multilingual-routes/issues/23)
- Added `localizedRoute` macro to `Illuminate\Routing\Redirector` [#24](https://github.com/chinleung/laravel-multilingual-routes/issues/24)
- Added helper `current_route` [#25](https://github.com/chinleung/laravel-multilingual-routes/issues/25)

## [v1.4.3 (2020-01-09)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.4.2...v1.4.3)

- Fix home page not being prefixed properly

## [v1.4.2 (2019-12-18)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.4.1...v1.4.2)

- Revert the locale detection changes through the `Accept-Language` [#16](https://github.com/chinleung/laravel-multilingual-routes/pull/15#issuecomment-567058440)

## [v1.4.1 (2019-12-16)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.4.0...v1.4.1)

- Detect locale of the request through `Accept-Language` [#15](https://github.com/chinleung/laravel-multilingual-routes/pull/15)

## [v1.4.0 (2019-12-02)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.3.0...v1.4.0)

- Added configuration `MULTILINGUAL_ROUTES_DEFAULT_LOCALE` to customize the default locale [#14](https://github.com/chinleung/laravel-multilingual-routes/issues/14)
- Changed `MULTILINGUAL_ROUTES_PREFIX_FALLBACK` to `MULTILINGUAL_ROUTES_PREFIX_DEFAULT`

## [v1.3.0 (2019-10-23)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.2.3...v1.3.0)

- Added support for route constraints  [#12](https://github.com/chinleung/laravel-multilingual-routes/issues/12)

## [v1.2.3 (2019-10-10)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.2.2...v1.2.3)

- Fixed view route registration throwing no action logic exception  [#10](https://github.com/chinleung/laravel-multilingual-routes/issues/10)

## [v1.2.2 (2019-09-20)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.2.1...v1.2.2)

- Fixed registration for routes with identical keys  [#9](https://github.com/chinleung/laravel-multilingual-routes/issues/9)

## [v1.2.1 (2019-09-19)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.2.0...v1.2.1)

- Fixed registration for routes with identical keys  [#8](https://github.com/chinleung/laravel-multilingual-routes/issues/8)

## [v1.2.0 (2019-09-07)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.1.0...v1.2.0)

- Added support for Laravel 6

## [v1.1.0 (2019-08-06)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.0.2...v1.1.0)

- Added support for view routes  [#5](https://github.com/chinleung/laravel-multilingual-routes/issues/5)

## [v1.0.2 (2019-08-01)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.0.1...v1.0.2)

- Fixed home page not registering properly  [#3](https://github.com/chinleung/laravel-multilingual-routes/issues/3)

## [v1.0.1 (2019-07-30)](https://github.com/chinleung/laravel-multilingual-routes/compare/v1.0.0...v1.0.1)

- Require `chinleung/laravel-locales` as dependency
- Moved `laravel-multilingual-routes.locales` to `laravel-locales.supported` for locales configuration
- Added `MULTILINGUAL_ROUTES_PREFIX_FALLBACK` env to prefix fallback configuration

## v1.0.0 (2019-07-29)

- Initial release
