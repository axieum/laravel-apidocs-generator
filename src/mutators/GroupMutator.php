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
                                ->mapToDictionary(function ($tag) {
                                    // Group descriptions by title (e.g. `[title => [descriptions...]]`)
                                    /** @var GroupTag $tag */
                                    return [$tag->getTitle() => (string)$tag->getDescription()];
                                })
                                ->map(function ($descriptions, $title) {
                                    // Merge groups and hence descriptions
                                    return [
                                        'title'       => $title,
                                        'description' => trim(join(PHP_EOL, $descriptions))
                                    ];
                                })
                                ->values()
                                ->toArray();

        $route->setMeta('groups', $groups ?: [
            'title'       => __('apidocs::docs.groups.default.title'),
            'description' => __('apidocs::docs.groups.default.description')
        ]);
    }
}
