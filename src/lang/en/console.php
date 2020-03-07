<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ApiDocs - Console Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during API documentation generation
    | for various messages that we need to display to the console.
    |
    */

    'begin'      => 'Generating API documentation...',
    'routes'     => 'Discovered :count routes',
    'preflight'  => 'Skipping route: [:methods] :uri - :reason',
    'preprocess' => 'Processing :routes routes across :groups groups...',
    'process'    => [
        'bar'   => ':name (:count routes) - :path',
        'error' => 'Unable to process group: :name (:count routes) - :path - :error',
        'table' => ['Group', 'Routes', 'Path', 'Size']
    ],
    'finish'     => 'Finished generating API documentation for :routes routes!'
];
