<?php

namespace Axieum\ApiDocs\mutators;

use Illuminate\Routing\Route;

interface RouteMutator
{
    /**
     * Mutates a given route.
     *
     * @param Route $route matched route for documentation generation
     */
    public static function mutate(Route $route): void;
}
