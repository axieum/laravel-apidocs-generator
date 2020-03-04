<?php

namespace Axieum\ApiDocs\util;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlock;

class DocBlockHelper
{
    /**
     * Filters a given docblock for group tags.
     *
     * @param DocBlock $docblock docblock with group tags
     * @param string   $class    tag class to filter on
     * @return Collection<DocBlock\Tag> group tags in the given docblock
     */
    public static function getTagsByClass(DocBlock $docblock, string $class): Collection
    {
        return collect($docblock->getTags())->filter(function ($tag) use ($class) {
            return $tag instanceof $class;
        });
    }
}
