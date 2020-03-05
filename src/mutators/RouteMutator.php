<?php

namespace Axieum\ApiDocs\mutators;

use Axieum\ApiDocs\util\DocRoute;

interface RouteMutator
{
    /**
     * Mutates a given route.
     *
     * @param DocRoute $route matched route for documentation generation
     */
    public static function mutate(DocRoute $route): void;
}
