<?php

return [
    /*
     * Your API path. By default, all routes starting with this path will be added to the docs.
     * If you need to change this behavior, you can add your custom routes resolver using `Scramble::routes()`.
     */
    'api_path' => 'api/v1',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    'info' => [
        'version' => env('API_VERSION', '1.0.0'),
        'title' => 'Currency Exchange API',
        'description' => 'API Documentation for Currency Exchange Service',
        'contact' => [
            'name' => 'API Support',
            'email' => 'support@example.com',
        ],
        'license' => [
            'name' => 'MIT',
            'url' => 'https://opensource.org/licenses/MIT',
        ],
    ],

    'servers' => [
        [
            'url' => env('APP_URL'),
            'description' => 'Local Environment',
        ],
    ],

    'middleware' => [
        'web',
    ],

    'extensions' => [],
];
