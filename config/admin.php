<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel admin name
    |--------------------------------------------------------------------------
    |
    | This value is the name of laravel admin, This setting is displayed on the
    | login page.
    |
    */
    'name' => env('ADMIN_NAME', 'Admin'),

    /*
    |--------------------------------------------------------------------------
    | Laravel admin logo
    |--------------------------------------------------------------------------
    |
    | The logo of all admin pages. You can also set it as an image by using a
    | `img` tag, eg '<img src="http://logo-url" alt="Admin logo">'.
    |
    */
    'logo' => env('ADMIN_LOGO'),

    /*
    |--------------------------------------------------------------------------
    | Laravel admin helpers
    |--------------------------------------------------------------------------
    |
    | This value is the path of laravel admin helpers file.
    |
    */
    'helpers' => app_path('Admin/helpers.php'),

    /*
    |--------------------------------------------------------------------------
    | Laravel admin install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the controller and routing configuration
    | files of the administration page. The default is `app/Admin`, which must
    | be set before running `artisan admin::install` to take effect.
    |
    */
    'directory' => app_path('Admin'),

    /*
    |--------------------------------------------------------------------------
    | Laravel admin index controller
    |--------------------------------------------------------------------------
    |
    | This value is the path of laravel admin index controller.
    |
    */
    'index' => App\Admin\Controllers\Extend\IndexController::class,

    /*
    |--------------------------------------------------------------------------
    | Laravel admin route settings
    |--------------------------------------------------------------------------
    |
    | The routing configuration of the admin page, including the path prefix,
    | the controller namespace, the default middleware, and the domain. If you
    | want to access through the root path, just set the prefix to empty string.
    |
    */
    'route' => [

        'prefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),

        'namespace' => 'App\\Admin\\Controllers',

        'middleware' => ['web', 'admin'],

        'domain' => env('ADMIN_ROUTE_DOMAIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel admin middleware settings
    |--------------------------------------------------------------------------
    |
    | The middleware configuration of the admin page.
    |
    */
    'middleware' => [
        App\Admin\Middlewares\Bootstrap::class,
        App\Admin\Middlewares\Auth::class,
        App\Admin\Middlewares\Permission::class,
        App\Admin\Middlewares\RequestedWith::class,
        App\Admin\Middlewares\LogOperation::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel admin resource directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the blade asset data.
    |
    */
    'resource' => [
        'view' => 'admin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel admin front-end vendor install directory
    |--------------------------------------------------------------------------
    |
    | The installation directory of the front-end asset data.
    |
    */
    'vendor' => [

        'path' => env('ADMIN_VENDOR_PATH', 'vendor'),

        'framework' => env('ADMIN_VENDOR_FRAMEWORK', 'Pear-Admin'),

        'plugin' => env('ADMIN_VENDOR_PLUGIN'),

        'config' => [
            'file' => env('ADMIN_VENDOR_CONFIG_FILE', 'config/pear.config.json'),
            'url' => env('ADMIN_VENDOR_CONFIG_URL', 'config'),
        ],

        'view' => [
            'forbidden' => '/exception/403',
            'notFound' => '/exception/404',
            'error' => '/exception/500',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel admin auth setting
    |--------------------------------------------------------------------------
    |
    | Authentication settings for all admin pages. Include an authentication
    | guard and a user provider setting of authentication driver.
    |
    | You can specify a controller for `login` `logout` and other auth routes.
    |
    */
    'auth' => [

        'guard' => 'admin',

        // Add "remember me" to login form
        'remember' => env("ADMIN_REMEMBER", env("APP_ENV", "production") != "production"),

        // Add captcha to login form
        'verification' => env('ADMIN_CAPTCHA', env("APP_ENV", "production") == "production"),

        // Redirect to the specified URI when user is not authorized.
        'redirect_to' => '/auth/index',

        'login' => '/auth/login',
    
        'captcha' => '/auth/captcha',

        'logout' => '/auth/logout',

        // The specified URI for user profile
        'profile' => '/auth/profile',

        // The URIs that should be excluded from authorization.
        'excepts' => [
            '/auth/index',
            '/auth/captcha',
            '/auth/login',
            '/auth/logout',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel admin database settings
    |--------------------------------------------------------------------------
    |
    | Here are database settings for laravel admin builtin model & tables.
    |
    */
    'database' => [

        // Database connection for following tables.
        'connection' => 'admin',

        // User tables and model.
        'user_table' => 'user',
        'user_model' => App\Admin\Models\Admin\UserModel::class,

        // Pivot table
        'user_role_table' => 'user_role',
        'user_role_model' => App\Admin\Models\Admin\UserRoleModel::class,

        // Role table and model.
        'role_table' => 'role',
        'role_model' => App\Admin\Models\Admin\RoleModel::class,

        'role_permission_table' => 'role_permission',
        'role_permission_model' => App\Admin\Models\Admin\RolePermissionModel::class,

        // Permission table and model.
        'permission_table' => 'permission',
        'permission_model' => App\Admin\Models\Admin\PermissionModel::class,

        // Menu table and model.
        'menu_table' => 'menu',
        'menu_model' => App\Admin\Models\Admin\MenuModel::class,

        // Log table and model.
        'operation_log_table' => 'operation_log',
        'operation_log_model' => App\Admin\Models\Admin\OperationLogModel::class,

        // Channel table and model.
        'channel_table' => 'channel',
        'channel_model' => App\Admin\Models\Extend\ChannelModel::class,

        // Role channel tables and model.
        'role_channel_table' => 'role_channel',
        'role_channel_model' => App\Admin\Models\Extend\RoleChannelModel::class,

        // Server table and model.
        'channel_server_table' => 'channel_server',
        'channel_server_model' => App\Admin\Models\Extend\ChannelServerModel::class,

        // Server table and model.
        'server_table' => 'server',
        'server_model' => App\Admin\Models\Extend\ServerModel::class,

        // Role server tables and model.
        'role_server_table' => 'role_server',
        'role_server_model' => App\Admin\Models\Extend\RoleServerModel::class,

        // Pivot table for table above.
        'configure_file_table' => 'configure_file',
        'configure_file_model' => App\Admin\Models\GameConfigure\ConfigureFileModel::class,

        // Pivot table for table above.
        'configure_table_table' => 'configure_table',
        'configure_table_model' => App\Admin\Models\GameConfigure\ConfigureTableModel::class,

        // Pivot table for table above.
        'import_log_table' => 'import_log',
        'import_log_model' => App\Admin\Models\GameConfigure\ImportLogModel::class,

        // Pivot table for table above.
        'user_manage_table' => 'user_manage',
        'user_manage_model' => App\Admin\Models\Operation\UserManageModel::class,

        // Pivot table for table above.
        'user_chat_manage_table' => 'user_chat_manage',
        'user_chat_manage_model' => App\Admin\Models\Operation\UserChatManageModel::class,

        // Pivot table for table above.
        'mail_table' => 'mail',
        'mail_model' => App\Admin\Models\Operation\MailModel::class,

        // Pivot table for table above.
        'notice_table' => 'notice',
        'notice_model' => App\Admin\Models\Operation\NoticeModel::class,

        // Pivot table for table above.
        'ssh_key_table' => 'ssh_key',
        'ssh_key_model' => App\Admin\Models\Assistant\SSHKeyModel::class,

        // The seeder excepts
        'excepts' => [
            'user',
            'user_role',
            'operation_log',
            'channel',
            'role_channel',
            'server',
            'role_server',
            'configure_file',
            'configure_table',
            'import_log',
            'user_manage',
            'user_chat_manage',
            'mail',
            'notice',
            'ssh_key',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel admin request trusted proxies setting
    |--------------------------------------------------------------------------
    |
    | The trusted proxies setting for admin.
    |
    */

    'proxies' => [
        'headers' => \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR,
        'addresses' => ['127.0.0.1'],
    ],

    /*
    |--------------------------------------------------------------------------
    | User operation log setting
    |--------------------------------------------------------------------------
    |
    | By setting this option to open or close operation log in laravel admin.
    |
    */
    'operation_log' => [

        'enable' => env('ADMIN_OPERATION_LOG', false),

        /*
         * Only logging allowed methods in the list
         */
        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        /*
         * Routes that will not log to database.
         *
         * All method to path like: /admin/logs
         * or specific method to path like: get:/admin/logs.
         */
        'excepts' => [
            '/admin/log',
        ],
    ],

];
