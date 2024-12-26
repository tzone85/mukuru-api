<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

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

    /*
     * Define the theme of the documentation.
     * Available options are `light` and `dark`.
     */
    'theme' => 'dark',

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '1.0.0'),

        /*
         * Description rendered on the home page of the API documentation (`/docs/api`).
         */
        'description' => 'Currency Exchange API Documentation',

        'title' => 'Currency Exchange API',

        'contact' => [
            'name' => 'API Support',
            'email' => 'support@example.com',
        ],

        'license' => [
            'name' => 'MIT',
            'url' => 'https://opensource.org/licenses/MIT',
        ],
    ],

    /*
     * Customize Stoplight Elements UI
     */
    'ui' => [
        /*
         * Hide the `Try It` feature. Enabled by default.
         */
        'hide_try_it' => false,

        /*
         * URL to an image that displays as a small square logo next to the title, above the table of contents.
         */
        'logo' => '',

        /*
         * Use to fetch the credential policy for the Try It feature. Options are: omit, include (default), and same-origin
         */
        'try_it_credentials_policy' => 'include',
    ],

    /*
     * The list of servers of the API. By default, when empty, server URL will be created from
     * the current URL when rendering the documentation.
     */
    'servers' => [
        [
            'url' => env('APP_URL'),
            'description' => 'Local Environment',
        ],
    ],

    /*
     * Map your authentication to OpenAPI authentication definitions.
     */
    'auth' => [
        /*
         * Define OpenAPI security scheme type, etc.
         */
        'bearer_token' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ],
    ],

    /*
     * The list of paths to exclude from the documentation.
     */
    'exclude_paths' => [
        '/docs/api',
        '/docs/api.json',
    ],

    /*
     * Directory for storing generated OpenAPI documentation.
     */
    'storage' => storage_path('api-docs'),

    /*
     * List of middleware to be applied to the docs endpoint.
     */
    'middleware' => [
        'web',
    ],

    /*
     * Should the package automatically build the documentation when accessed.
     */
    'auto_build' => env('APP_ENV') !== 'production',
];
