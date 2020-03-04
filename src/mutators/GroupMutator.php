<?php

namespace Axieum\ApiDocs\mutators;

use Axieum\ApiDocs\tags\GroupTag;
use Axieum\ApiDocs\util\DocBlockHelper;
use Illuminate\Routing\Route;
use phpDocumentor\Reflection\DocBlock;

/**
 * Route mutator for assigning route groups.
 */
class GroupMutator implements RouteMutator
{
    /**
     * @inheritDoc
     */
    public static function mutate(Route $route): void
    {
        /** @var DocBlock $controllerDocBlock */
        $controllerDocBlock = $route->controllerDocBlock;
        /** @var DocBlock $actionDocBlock */
        $actionDocBlock = $route->actionDocBlock;

        $route->meta['groups'] = DocBlockHelper::getTagsByClass($controllerDocBlock, GroupTag::class)
                                               ->concat(
                                                   DocBlockHelper::getTagsByClass($actionDocBlock, GroupTag::class))
                                               ->map(function ($tag) {
                                                   return (string)$tag;
                                               })
                                               ->unique()
                                               ->toArray();
    }
}
