<?php

namespace Axieum\ApiDocs\preflight;

use Axieum\ApiDocs\tags\HiddenTag;
use Axieum\ApiDocs\util\DocBlockHelper;
use Illuminate\Routing\Route;
use phpDocumentor\Reflection\DocBlock;

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
    public static function apply(Route $route): ?string
    {
        /** @var DocBlock|null $controllerDocBlock */
        $controllerDocBlock = $route->controllerDocBlock;
        /** @var DocBlock|null $actionDocBlock */
        $actionDocBlock = $route->actionDocBlock;

        // Fetch all "hidden" tags on both controller and action
        $hiddenTags = DocBlockHelper::getTagsByClass($controllerDocBlock, HiddenTag::class)
                                    ->concat(DocBlockHelper::getTagsByClass($actionDocBlock, HiddenTag::class));

        // Reject route if one or more "hidden" tags exist
        return $hiddenTags->isNotEmpty() ? __('apidocs::preflight.hidden') : null;
    }
}
