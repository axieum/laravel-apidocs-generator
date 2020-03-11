<?php

namespace Axieum\ApiDocs\mutators;

use Axieum\ApiDocs\tags\GroupTag;
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
        // Extract specified route groups
        $groups = $route->getTagsByClass(GroupTag::class)
                        ->mapToDictionary(function ($tag) {
                            // Group descriptions by title (e.g. `[title => [descriptions...]]`)
                            /** @var GroupTag $tag */
                            return [$tag->getTitle() => (string)$tag->getDescription()];
                        })
                        ->map(function ($descriptions, $title) {
                            // Merge groups and hence descriptions
                            return ['title' => $title, 'description' => trim(join(PHP_EOL, $descriptions))];
                        })
                        ->values()
                        ->whenEmpty(function () {
                            // Supply default group if no groups specified
                            return collect([
                                'title'       => __('apidocs::docs.groups.default.title'),
                                'description' => __('apidocs::docs.groups.default.description')
                            ]);
                        });

        $route->setMeta('groups', $groups);
    }
}
