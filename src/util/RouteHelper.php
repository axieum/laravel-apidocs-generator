<?php

namespace Axieum\ApiDocs\util;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteHelper
{
    /**
     * Filters and returns registered routes that match supplied patterns.
     *
     * @param array $rules  patterns of route names/uris to include
     * @param array $hidden patterns of route names/uris to exclude
     * @return array|\Illuminate\Routing\Route filtered routes
     */
    public static function getRoutes($rules = [], $hidden = [])
    {
        /** @var array|\Illuminate\Routing\Route $routes */
        $routes = [];

        /** @var \Illuminate\Routing\Route $route */
        foreach (Route::getRoutes() as $route) {
            // Skip routes that should be hidden
            if (Str::is($hidden, $route->getName()) || Str::is($hidden, $route->uri()))
                continue;

            // Add routes that match the criteria
            if (Str::is($rules, $route->getName()) || Str::is($rules, $route->uri()))
                $routes[] = $route;
        }

        return $routes;
    }
}
