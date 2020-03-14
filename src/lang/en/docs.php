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
        'status' => ':status :text|:status Unknown Status'
    ],

    'groups' => [
        'default' => [
            'title'       => 'General',
            'description' => ''
        ]
    ]
];
