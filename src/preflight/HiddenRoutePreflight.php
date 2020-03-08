<?php

namespace Axieum\ApiDocs\preflight;

use Axieum\ApiDocs\tags\HiddenTag;
use Axieum\ApiDocs\util\DocRoute;

/**
 * Route preflight check for hiding routes annotated as hidden from documentation.
 */
class HiddenRoutePreflight implements RoutePreflight
{
    /**
     * @inheritDoc
     */
    public static function degree(): string
    {
        return PreflightDegree::DEFAULT;
    }

    /**
     * @inheritDoc
     */
    public static function apply(DocRoute $route): ?string
    {
        // Reject route if one or more "hidden" tags exist
        return $route->getTagsByClass(HiddenTag::class)->isNotEmpty() ? __('apidocs::preflight.hidden') : null;
    }
}
