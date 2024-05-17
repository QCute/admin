<?php

namespace App\Admin\Controllers;

use App\Admin\Services\Auth\AuthService;

class HomeController extends Controller
{
    public function index()
    {
        $environments = self::environments();
        $dependencies = self::dependencies();
        return admin_view('home', ['environments' => $environments, 'dependencies' => $dependencies]);
    }

    public static function environments()
    {
        return [
            ['name' => 'PHP version',       'value' => 'PHP/' . PHP_VERSION],
            ['name' => 'Laravel version',   'value' => app()->version()],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => $_SERVER['SERVER_SOFTWARE'] ?? ''],

            ['name' => 'Cache driver',      'value' => config('cache.default')],
            ['name' => 'Session driver',    'value' => config('session.driver')],
            ['name' => 'Queue driver',      'value' => config('queue.default')],

            ['name' => 'Timezone',          'value' => config('app.timezone')],
            ['name' => 'Locale',            'value' => config('app.locale')],
            ['name' => 'Env',               'value' => config('app.env')],
            ['name' => 'Debug',             'value' => config('app.debug') ? 'true' : 'false'],
            ['name' => 'URL',               'value' => config('app.url')],
        ];
    }

    public static function dependencies()
    {
        $json = file_get_contents(base_path('composer.json'));

        return json_decode($json, true)['require'];
    }
}
