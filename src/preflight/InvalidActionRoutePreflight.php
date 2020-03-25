<?php

namespace Axieum\ApiDocs\preflight;

use Axieum\ApiDocs\util\DocRoute;
use Axieum\ApiDocs\util\RouteHelper;

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
    public static function apply(DocRoute $route): ?string
    {
        // Reject route if action is a (unsupported) closure
        if (RouteHelper::isClosure($route->getRoute()))
            return __('apidocs::preflight.closure');

        // Reject route if invalid controller action
        if (!RouteHelper::hasValidControllerMethod($route->getRoute()))
            return __('apidocs::preflight.missing_action');

        // Reject route if no docblock(s) were injected
        if (!$route->hasDocBlock('action'))
            return __('apidocs::preflight.non_documented');

        return null;
    }
}
