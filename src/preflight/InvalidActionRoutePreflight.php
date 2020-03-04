<?php

namespace Axieum\ApiDocs\preflight;

use Axieum\ApiDocs\util\RouteHelper;
use Illuminate\Routing\Route;

/**
 * Route preflight check for when a route's action is invalid.
 */
class InvalidActionRoutePreflight implements RoutePreflight
{
    /**
     * @inheritDoc
     */
    public static function degree(): string
    {
        return PreflightDegree::WARNING;
    }

    /**
     * @inheritDoc
     */
    public static function apply(Route $route): ?string
    {
        // Reject route if action is a (unsupported) closure
        if (RouteHelper::isClosure($route))
            return __('apidocs::preflight.closure');

        // Reject route if invalid controller action
        if (!RouteHelper::hasValidControllerMethod($route))
            return __('apidocs::preflight.missing_action');

        // Reject route if no docblock(s) were injected
        if (!isset($route->controllerDocBlock) && !isset($route->actionDocBlock))
            return __('apidocs::preflight.non_documented');

        return null;
    }
}
