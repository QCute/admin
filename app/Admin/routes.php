<?php


use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Encore\Admin\Facades\Admin;

Admin::routes();

$router = Route::group([
    'domain'        => config('admin.route.domain', env('ADMIN_ROUTE_DOMAIN', 'admin')),
    'prefix'        => config('admin.route.prefix', env('ADMIN_ROUTE_PREFIX', 'admin')),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    // Dashboard
    $router->get('/',                                'HomeController@index')->name('admin.home');
    $router->get('/example',                         'ExampleController@index')->name('admin.home');

    // Switch Server Database
    $router->get('/switch-server',                   'SwitchServerController@index')->name('admin.home');
    
    // User Active Statistics
    $router->get('/user-online',                     'ActiveStatisticsControllers\\UserOnlineController@index')->name('admin.home');
    $router->get('/user-register',                   'ActiveStatisticsControllers\\UserRegisterController@index')->name('admin.home');
    $router->get('/user-login',                      'ActiveStatisticsControllers\\UserLoginController@index')->name('admin.home');
    $router->get('/user-retain',                     'ActiveStatisticsControllers\\UserRetainController@index')->name('admin.home');
    $router->get('/user-survival',                   'ActiveStatisticsControllers\\UserSurvivalController@index')->name('admin.home');
    $router->get('/user-loss',                       'ActiveStatisticsControllers\\UserLossController@index')->name('admin.home');

    // User Recharge Statistics
    $router->get('/user-recharge',                   'RechargeStatisticsControllers\\UserRechargeController@index')->name('admin.home');
    $router->get('/recharge-ratio',                  'RechargeStatisticsControllers\\RechargeRatioController@index')->name('admin.home');
    $router->get('/recharge-distribution',           'RechargeStatisticsControllers\\RechargeDistributionController@index')->name('admin.home');
    $router->get('/first-recharge-time-distribution','RechargeStatisticsControllers\\FirstRechargeTimeDistributionController@index')->name('admin.home');


    // Game Data(user/configure/log)
    $router->get('/user-data',                       'GameDataControllers\\TableDataListController@showRole')->name('admin.home');
    $router->get('/configure-data',                  'GameDataControllers\\TableDataListController@showConfigure')->name('admin.home');
    $router->get('/log-data',                        'GameDataControllers\\TableDataListController@showLog')->name('admin.home');
    $router->get('/table-viewer',                    'GameDataControllers\\TableDataViewerController@index')->name('admin.home');
    $router->get('/client-error-log',                'GameDataControllers\\ClientErrorLogController@index')->name('admin.home');
    $router->post('/client-error-log',               'GameDataControllers\\ClientErrorLogController@index')->name('admin.home');

    // Game Configure Data
    $router->get('/configure-table',                 'GameConfigureControllers\\ConfigureTableController@index')->name('admin.home');
    $router->post('/configure-table',                'GameConfigureControllers\\ConfigureTableController@index')->name('admin.home');
    $router->get('/erl-configure',                   'GameConfigureControllers\\ConfigureListController@showErl')->name('admin.home');
    $router->get('/lua-configure',                   'GameConfigureControllers\\ConfigureListController@showLua')->name('admin.home');
    $router->get('/js-configure',                    'GameConfigureControllers\\ConfigureListController@showJs')->name('admin.home');

    // Game Server Manage
    $router->get('/server-list-manage',              'ServerManageControllers\\ServerListManageController@index')->name('admin.home');
    $router->post('/server-list-manage',             'ServerManageControllers\\ServerListManageController@index')->name('admin.home');
    $router->get('/server-list-manage-public',       'ServerManageControllers\\ServerListManageController@public')->name('admin.home');
    $router->get('/user-manage',                     'ServerManageControllers\\UserManageController@index')->name('admin.home');
    $router->post('/user-manage',                    'ServerManageControllers\\UserManageController@index')->name('admin.home');
    $router->get('/user-manage-search',              'ServerManageControllers\\UserManageController@search')->name('admin.home');
    $router->get('/server-mail',                     'ServerManageControllers\\ServerMailController@index')->name('admin.home');
    $router->post('/server-mail',                    'ServerManageControllers\\ServerMailController@index')->name('admin.home');
    $router->get('/server-notice',                   'ServerManageControllers\\ServerNoticeController@index')->name('admin.home');
    $router->post('/server-notice',                  'ServerManageControllers\\ServerNoticeController@index')->name('admin.home');
    $router->get('/open-server',                     'ServerManageControllers\\OpenServerController@index')->name('admin.home');
    $router->post('/open-server',                    'ServerManageControllers\\OpenServerController@index')->name('admin.home');
    $router->get('/merge-server',                    'ServerManageControllers\\MergeServerController@index')->name('admin.home');
    $router->post('/merge-server',                   'ServerManageControllers\\MergeServerController@index')->name('admin.home');
    $router->get('/user-impeach',                    'ServerManageControllers\\ImpeachController@index')->name('admin.home');
    $router->post('/user-impeach',                   'ServerManageControllers\\ImpeachController@index')->name('admin.home');
    $router->get('/sensitive-word',                  'ServerManageControllers\\SensitiveWordController@index')->name('admin.home');
    $router->post('/sensitive-word',                 'ServerManageControllers\\SensitiveWordController@index')->name('admin.home');

});
