<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel web Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of laravel web
    |
    */
    'name' => env('WEB_NAME', 'web'),

    /*
    |--------------------------------------------------------------------------
    | Laravel web helpers
    |--------------------------------------------------------------------------
    |
    | This value is the path of laravel web helpers file.
    |
    */
    'helpers' => app_path('Http/helpers.php'),

    /*
    |--------------------------------------------------------------------------
    | Laravel web install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the web.
    |
    */
    'directory' => app_path('Http'),

    /*
    |--------------------------------------------------------------------------
    | Laravel web route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the web, including the path prefix,
    | the controller namespace, the default middleware, and the domain. If you
    | want to access through the root path, just set the prefix to empty string.
    |
    */
    'route' => [

        'prefix' => env('WEB_ROUTE_PREFIX', ''),

        'namespace' => 'App\\Http\\Controllers',

        'middleware' => ['web'],

        'domain' => env('WEB_ROUTE_DOMAIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel web middleware settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the web, including the path prefix,
    | the controller namespace, the default middleware, and the domain. If you
    | want to access through the root path, just set the prefix to empty string.
    |
    */
    'middleware' => [
        App\Http\Middlewares\Bootstrap::class,
        App\Http\Middlewares\Log::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel web auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all web
    |
    |
    */
    'auth' => [

        // Add "remember me" to login form
        'remember' => env("WEB_REMEMBER", env("APP_ENV", "production") != "production"),

        // The URIs that should be excluded from authorization.
        'excepts' => [

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel web database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for laravel web builtin model & tables.
    |
    */
    'database' => [

        // Database connection for following tables.
        'connection' => 'web',

        // Navigation tables and model.
        'navigation_table' => 'navigation',
        'navigation_model' => App\Http\Models\NavigationModel::class,

        // Log tables and model.
        'log_table' => 'log',
        'log_model' => App\Http\Models\LogModel::class,

        // The seeder excepts
        'excepts' => [
            'log'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Web request trusted proxies setting
    |--------------------------------------------------------------------------
    |
    | The trusted proxies setting for web.
    |
    */

    'proxies' => [
        'headers' => \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR,
        'addresses' => ['127.0.0.1'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Web request log setting
    |--------------------------------------------------------------------------
    |
    | By setting this option to open or close web log in laravel web.
    |
    */
    'log' => [

        'enable' => false,

        /*
         * Only logging allowed methods in the list
         */
        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        /*
         * Routes that will not log to database.
         *
         * All method to path like: web/auth/logs
         */
        'excepts' => [
        ],
    ],
];
