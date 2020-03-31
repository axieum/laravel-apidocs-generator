<?php

namespace Axieum\ApiDocs\util;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionParameter;

class RouteHelper
{
    /**
     * Filters and returns registered routes.
     *
     * @param array|null $matches route uri patterns must match with (at least one)
     * @param array|null $hides   route uri patterns to avoid
     * @return Collection<\Illuminate\Routing\Route> filtered routes
     */
    public static function getRoutes(?array $matches = [], ?array $hides = []): Collection
    {
        /** @var Collection<\Illuminate\Routing\Route> $routes all registered routes */
        $routes = collect(Route::getRoutes()->getRoutes());

        // If no patterns provided, return all routes
        if (empty($matches) && empty($hides)) return $routes;

        // Filter matched routes
        return $routes->filter(function ($route) use ($matches, $hides) {
            /** @var \Illuminate\Routing\Route $route */

            // Skip routes that should be hidden
            if (Str::is($hides, $route->getName()) || Str::is($hides, $route->uri()))
                return false;

            // Allow routes that match the criteria
            return Str::is($matches, $route->getName()) || Str::is($matches, $route->uri());
        });
    }

    /**
     * Returns the DocBlock associated with the given route's controller.
     *
     * @param \Illuminate\Routing\Route $route   route with controller
     * @param DocBlockFactory|null      $factory overrides the default factory
     * @return DocBlock|null DocBlock instance for route's controller or null if not exists
     */
    public static function getControllerDocBlock(\Illuminate\Routing\Route $route,
                                                 ?DocBlockFactory $factory = null): ?DocBlock
    {
        if (is_null($factory)) $factory = DocBlockFactory::createInstance();

        try {
            $controller = $route->getController();
            return $factory->create(new \ReflectionClass($controller));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the DocBlock associated with the given route's action.
     *
     * @param \Illuminate\Routing\Route $route   route with action
     * @param DocBlockFactory|null      $factory overrides the default factory
     * @return DocBlock|null DocBlock instance for route's action or null if not exists
     */
    public static function getActionDocBlock(\Illuminate\Routing\Route $route,
                                             ?DocBlockFactory $factory = null): ?DocBlock
    {
        if (is_null($factory)) $factory = DocBlockFactory::createInstance();

        try {
            $controller = $route->getController();
            $action = (new \ReflectionClass($controller))->getMethod($route->getActionMethod());
            return $factory->create($action);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the DocBlock associated with the given route's form request.
     *
     * @param \Illuminate\Routing\Route $route   route with type-hinted form request
     * @param DocBlockFactory|null      $factory overrides the default factory
     * @return DocBlock|null DocBlock instance for route's form request or null if not exists
     */
    public static function getRequestDocBlock(\Illuminate\Routing\Route $route,
                                              ?DocBlockFactory $factory = null): ?DocBlock
    {
        if (is_null($factory)) $factory = DocBlockFactory::createInstance();

        try {
            $controller = $route->getController();
            $action = (new \ReflectionClass($controller))->getMethod($route->getActionMethod());

            // Attempt to find the first Form Request parameter
            $request = collect($action->getParameters())->first(function ($param) {
                /** @var ReflectionParameter $param */
                return optional($param->getClass())->isSubclassOf(FormRequest::class);
            });

            return $factory->create($request->getClass());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Determines whether a given route's action is a closure (not a controller).
     *
     * @param \Illuminate\Routing\Route $route route with specified action
     * @return bool true if the route's action is a closure
     */
    public static function isClosure(\Illuminate\Routing\Route $route): bool
    {
        return $route->getAction('uses') instanceof \Closure;
    }

    /**
     * Determines whether a given route's action points to a valid controller method.
     *
     * @param \Illuminate\Routing\Route $route
     * @return bool true if the route's action points to a valid controller method
     */
    public static function hasValidControllerMethod(\Illuminate\Routing\Route $route): bool
    {
        $action = $route->getAction('uses');

        return !is_null($action) && is_callable(explode('@', $action));
    }
}
