<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ApiDocs - Documentation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during API documentation generation
    | for various messages.
    |
    */

    'tags' => [
        'auth' => 'Requires authentication!',
    ],

    'requests' => [
        'title'      => 'Requests',
        'empty'      => 'There are no documented requests.',
        'url'        => [
            'title' => 'URL Parameters'
        ],
        'query'      => [
            'title' => 'Query Parameters'
        ],
        'body'       => [
            'title' => 'Body Parameters'
        ],
        'parameters' => [
            'required' => 'optional|required'
        ]
    ],

    'responses' => [
        'title'  => 'Responses',
        'empty'  => 'There are no documented responses.',
        'status' => ':status :text|:status Unknown Status'
    ],

    'groups' => [
        'default' => [
            'title'       => 'General',
            'description' => ''
        ]
    ]
];
