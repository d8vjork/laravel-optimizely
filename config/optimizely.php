<?php

return [

    'key' => env('OPTIMIZELY_KEY'),

    'datafile_path' => storage_path('features.json'),

    'webhook' => [
        'path' => '/webhooks/optimizely',
        'secret' => env('OPTIMIZELY_WEBHOOK_SECRET'),
    ],

];
