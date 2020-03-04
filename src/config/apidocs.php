<?php

return [
    'tags'      => [
        'query'          => \Axieum\ApiDocs\tags\QueryTag::class,
        'url'            => \Axieum\ApiDocs\tags\UrlTag::class,
        'body'           => \phpDocumentor\Reflection\DocBlock\Tags\Generic::class,
        'response'       => \phpDocumentor\Reflection\DocBlock\Tags\Generic::class,
        'group'          => \Axieum\ApiDocs\tags\GroupTag::class,
        'authentication' => \phpDocumentor\Reflection\DocBlock\Tags\Generic::class,
        'hidden'         => \Axieum\ApiDocs\tags\HiddenTag::class,
    ],
    'preflight' => [
        \Axieum\ApiDocs\preflight\InvalidActionRoutePreflight::class,
        \Axieum\ApiDocs\preflight\HiddenRoutePreflight::class
    ],
    'mutators'  => [
        \Axieum\ApiDocs\mutators\GroupMutator::class
    ],
    'groupBy'   => 'meta.groups',
    'routes'    => [
        'matches' => [
            '*'
        ],
        'hides'   => []
    ]
];
