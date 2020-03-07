<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Documentation Output
    |--------------------------------------------------------------------------
    |
    | This value is the file path naming convention used when saving.
    | generated documentation. You can use ':version' for the current API
    | version being used, and ':name' for the "slug" of the route group.
    |
    */

    'output' => 'docs/:version/:name.md',

    /*
    |--------------------------------------------------------------------------
    | Route Group By
    |--------------------------------------------------------------------------
    |
    | This value determines how routes will be grouped into renders. This
    | allows for flexibility in grouping routes based on your own properties.
    |
    */

    'groupBy' => 'meta.groups.*.title',

    /*
    |--------------------------------------------------------------------------
    | Route Matching
    |--------------------------------------------------------------------------
    |
    | This option controls which routes should be used for the documentation
    | generation based on their URI. Use 'matches' for routes to whitelist,
    | and 'hides' to blacklist (NB: hiding takes priority over matching).
    |
    */

    'routes' => [
        'matches' => [
            '*'
        ],
        'hides'   => []
    ],

    /*
    |--------------------------------------------------------------------------
    | DocBlock Tags
    |--------------------------------------------------------------------------
    |
    | These key-value pairs are custom DocBlock tags to be loaded and
    | processed. The key denotes the tag name (e.g. '@param') and its
    | value should be an implementation of a phpDocumentor Tag.
    |
    */

    'tags' => [
        'query'          => \Axieum\ApiDocs\tags\QueryTag::class,
        'url'            => \Axieum\ApiDocs\tags\UrlTag::class,
        'body'           => \phpDocumentor\Reflection\DocBlock\Tags\Generic::class,
        'response'       => \phpDocumentor\Reflection\DocBlock\Tags\Generic::class,
        'group'          => \Axieum\ApiDocs\tags\GroupTag::class,
        'authentication' => \phpDocumentor\Reflection\DocBlock\Tags\Generic::class,
        'hidden'         => \Axieum\ApiDocs\tags\HiddenTag::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Preflight Checks
    |--------------------------------------------------------------------------
    |
    | This option allows registering documentation generation "middleware",
    | to have the final say on whether a route should be processed or not.
    | NB: These can be leveraged to inspect DocBlock conventions, check
    | conflicts, etc.
    |
    */

    'preflight' => [
        \Axieum\ApiDocs\preflight\InvalidActionRoutePreflight::class,
        \Axieum\ApiDocs\preflight\HiddenRoutePreflight::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Mutators
    |--------------------------------------------------------------------------
    |
    | This option allows registering documented route mutators.
    | NB: These can be leveraged to inject metadata into a route for access
    | in rendering.
    |
    */

    'mutators' => [
        \Axieum\ApiDocs\mutators\GroupMutator::class
    ]

];
