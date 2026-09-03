<?php

namespace ChinLeung\MultilingualRoutes\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\UrlGenerator;

class RedirectController
{
    public const DESTINATION = 'multilingual_redirect_destination';

    /**
     * Invoke the controller method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\UrlGenerator  $url
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, UrlGenerator $url): RedirectResponse
    {
        $destination = $request->route()->getAction(self::DESTINATION);
        $route = (new Route('GET', $destination, [
            'as' => 'multilingual_route_redirect_destination',
        ]))->bind($request);

        $parameters = collect($request->route()->parameters())->only(
            $route->getCompiled()->getPathVariables()
        )->all();

        $destination = $url->toRoute($route, $parameters, false);

        if ($query = $request->getQueryString()) {
            $destination .= "?{$query}";
        }

        return new RedirectResponse($destination, 302);
    }
}
