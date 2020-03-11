<?php

namespace Axieum\ApiDocs\mutators;

use Axieum\ApiDocs\util\DocRoute;
use Illuminate\Support\Str;

/**
 * Route mutator for computing the state of route authentication.
 */
class AuthMutator implements RouteMutator
{
    /**
     * @inheritDoc
     */
    public static function mutate(DocRoute $route): void
    {
        $auth = false;
        foreach ($route->getRoute()->gatherMiddleware() as $middleware)
            if ($auth = Str::startsWith($middleware, 'auth'))
                break;

        $route->setMeta('auth', $auth);
    }
}
