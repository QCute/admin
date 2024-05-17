<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel API Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of laravel api
    |
    */
    'name' => env('API_NAME', 'API'),

    /*
    |--------------------------------------------------------------------------
    | Laravel API helpers
    |--------------------------------------------------------------------------
    |
    | This value is the path of laravel API helpers file.
    |
    */
    'helpers' => app_path('Api/helpers.php'),

    /*
    |--------------------------------------------------------------------------
    | Laravel API install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the api.
    |
    */
    'directory' => app_path('Api'),

    /*
    |--------------------------------------------------------------------------
    | Laravel API route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the api, including the path prefix,
    | the controller namespace, the default middleware, and the domain. If you
    | want to access through the root path, just set the prefix to empty string.
    |
    */
    'route' => [

        'prefix' => env('API_ROUTE_PREFIX', 'api'),

        'namespace' => 'App\\Api\\Controllers',

        'middleware' => ['api'],

        'domain' => env('API_ROUTE_DOMAIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel API middleware settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the api, including the path prefix,
    | the controller namespace, the default middleware, and the domain. If you
    | want to access through the root path, just set the prefix to empty string.
    |
    */
    'middleware' => [
        App\Api\Middlewares\Bootstrap::class,
        App\Api\Middlewares\Log::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel API auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all api
    |
    |
    */
    'auth' => [

        // The URIs that should be excluded from authorization.
        'excepts' => [

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel API database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for laravel api builtin model & tables.
    |
    */
    'database' => [

        // Database connection for following tables.
        'connection' => 'api',

        // Server table and model.
        'server_table' => 'server',
        'server_model' => App\Api\Models\ServerModel::class,

        // Maintain Notice tables and model.
        'maintain_notice_table' => 'maintain_notice',
        'maintain_notice_model' => App\Api\Models\MaintainNoticeModel::class,

        // Impeach table
        'impeach_table' => 'impeach',
        'impeach_model' => App\Api\Models\ImpeachModel::class,

        // Client Error Log tables and model.
        'client_error_log_table' => 'client_error_log',
        'client_error_log_model' => App\Api\Models\ClientErrorLogModel::class,

        // Sensitive Word Data table and model.
        'sensitive_word_data_table' => 'sensitive_word_data',
        'sensitive_word_data_model' => App\Api\Models\SensitiveWordDataModel::class,

        // Log tables and model.
        'log_table' => 'log',
        'log_model' => App\Api\Models\LogModel::class,

        // The seeder excepts
        'excepts' => [
            'server',
            'maintain_notice',
            'impeach',
            'client_error_log',
            'sensitive_word_data',
            'log'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API request trusted proxies setting
    |--------------------------------------------------------------------------
    |
    | The trusted proxies setting for API.
    |
    */

    'proxies' => [
        'headers' => \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR,
        'addresses' => ['127.0.0.1'],
    ],

    /*
    |--------------------------------------------------------------------------
    | API request log setting
    |--------------------------------------------------------------------------
    |
    | By setting this option to open or close api log in laravel API.
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
         * All method to path like: api/auth/logs
         */
        'excepts' => [
        ],
    ],
];
