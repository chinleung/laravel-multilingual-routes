<?php

namespace ChinLeung\MultilingualRoutes\Controllers;

use Illuminate\Support\Facades\Response;

class ViewController
{
    /**
     * Invoke the controller method.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response
     */
    public function __invoke(string $view, array $data = [], int $status = 200, array $headers = []): \Illuminate\Http\Response
    {
        return Response::view($view, $data, $status, $headers);
    }
}
