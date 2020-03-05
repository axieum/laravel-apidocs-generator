<?php

namespace Axieum\ApiDocs\mutators;

use Axieum\ApiDocs\tags\GroupTag;
use Axieum\ApiDocs\util\DocBlockHelper;
use Axieum\ApiDocs\util\DocRoute;

/**
 * Route mutator for assigning route groups.
 */
class GroupMutator implements RouteMutator
{
    /**
     * @inheritDoc
     */
    public static function mutate(DocRoute $route): void
    {
        $controllerDocBlock = $route->getDocBlock('controller');
        $actionDocBlock = $route->getDocBlock('action');

        // Extract specified route groups from both controller and action docblock(s)
        $groups = DocBlockHelper::getTagsByClass($controllerDocBlock, GroupTag::class)
                                ->concat(DocBlockHelper::getTagsByClass($actionDocBlock, GroupTag::class))
                                ->map(function ($tag) {
                                    return (string)$tag;
                                })
                                ->unique()
                                ->toArray();

        $route->setMeta('groups', $groups ?: [__('apidocs::docs.groups.default')]);
    }
}
