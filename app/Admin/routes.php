<?php


use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Encore\Admin\Facades\Admin;

Admin::routes();

$router = Route::group([
    'domain'        => config('admin.route.domain'),
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    // Dashboard
    $router->get('/',                                'HomeController@index')->name('admin.home');
    $router->get('/example',                         'ExampleController@index')->name('admin.home');

    // Switch Server Database

    $router->get('/switch-server',                   'SwitchServerController@index')->name('admin.home');

    // assistant
    $router->get('/key-assistant',                   'AssistantControllers\\KeyAssistantController@index')->name('admin.home');
    $router->post('/key-assistant-generate',         'AssistantControllers\\KeyAssistantController@generate')->name('admin.home');
    $router->get('/configure-assistant',             'AssistantControllers\\ConfigureAssistantController@index')->name('admin.home');

    // User Active Statistics
    $router->get('/user-online',                     'ActiveStatisticsControllers\\UserOnlineController@index')->name('admin.home');
    $router->get('/user-register',                   'ActiveStatisticsControllers\\UserRegisterController@index')->name('admin.home');
    $router->get('/user-login',                      'ActiveStatisticsControllers\\UserLoginController@index')->name('admin.home');
    $router->get('/user-survival',                   'ActiveStatisticsControllers\\UserSurvivalController@index')->name('admin.home');
    $router->get('/daily-online-time',               'ActiveStatisticsControllers\\DailyOnlineTimeController@index')->name('admin.home');

    // User Recharge Statistics
    $router->get('/daily-recharge',                  'RechargeStatisticsControllers\\DailyRechargeController@index')->name('admin.home');
    $router->get('/recharge-rank',                   'RechargeStatisticsControllers\\RechargeRankController@index')->name('admin.home');
    $router->get('/recharge-ratio',                  'RechargeStatisticsControllers\\RechargeRatioController@index')->name('admin.home');
    $router->get('/recharge-distribution',           'RechargeStatisticsControllers\\RechargeDistributionController@index')->name('admin.home');
    $router->get('/first-recharge-time-distribution','RechargeStatisticsControllers\\FirstRechargeTimeDistributionController@index')->name('admin.home');

    // Game Data(user/configure/log)
    // $router->get('/user-data',                       'GameDataControllers\\TableDataListController@showRole')->name('admin.home');
    // $router->get('/configure-data',                  'GameDataControllers\\TableDataListController@showConfigure')->name('admin.home');
    // $router->get('/log-data',                        'GameDataControllers\\TableDataListController@showLog')->name('admin.home');

    $router->get('/user-data',                       'GameDataControllers\\TableDataListController@user');
    $router->get('/configure-data',                  'GameDataControllers\\TableDataListController@configure');
    $router->get('/log-data',                        'GameDataControllers\\TableDataListController@log');
    $router->get('/table-data-viewer',               'GameDataControllers\\TableDataViewerController@index');
    $router->resource('client-error-log',       'GameDataControllers\\ClientErrorLogController');

    // Game Configure Data
    $router->get('/configure-table',                 'GameConfigureControllers\\ConfigureTableController@index')->name('admin.home');
    $router->post('/configure-table',                'GameConfigureControllers\\ConfigureTableController@index')->name('admin.home');
    $router->get('/erl-configure',                   'GameConfigureControllers\\ConfigureListController@erl')->name('admin.home');
    $router->get('/lua-configure',                   'GameConfigureControllers\\ConfigureListController@lua')->name('admin.home');
    $router->get('/js-configure',                    'GameConfigureControllers\\ConfigureListController@js')->name('admin.home');

    // Game Server Manage
    $router->get('/server-list-manage',              'ServerManageControllers\\ServerListManageController@index')->name('admin.home');
    $router->post('/server-list-manage',             'ServerManageControllers\\ServerListManageController@index')->name('admin.home');
    $router->get('/server-list-manage-publish',      'ServerManageControllers\\ServerListManageController@publish')->name('admin.home');

    $router->get('/open-server',                     'ServerManageControllers\\OpenServerController@index')->name('admin.home');
    $router->post('/open-server',                    'ServerManageControllers\\OpenServerController@index')->name('admin.home');

    $router->get('/merge-server',                    'ServerManageControllers\\MergeServerController@index')->name('admin.home');
    $router->post('/merge-server',                   'ServerManageControllers\\MergeServerController@index')->name('admin.home');

    // Operation
    $router->get('/user-manage',                     'OperationControllers\\UserManageController@index')->name('admin.home');
    $router->post('/user-manage',                    'OperationControllers\\UserManageController@index')->name('admin.home');
    // $router->get('/user-manage-search',              'ServerManageControllers\\UserManageController@search')->name('admin.home');

    $router->get('/game-mail',                       'OperationControllers\\GameMailController@index')->name('admin.home');
    $router->post('/game-mail',                      'OperationControllers\\GameMailController@index')->name('admin.home');

    $router->get('/game-notice',                     'OperationControllers\\GameNoticeController@index')->name('admin.home');
    $router->post('/game-notice',                    'OperationControllers\\GameNoticeController@index')->name('admin.home');

    $router->resource('impeach',                'OperationControllers\\ImpeachController');
    $router->resource('maintain-notice',        'OperationControllers\\MaintainNoticeController');
    $router->resource('sensitive-word',         'OperationControllers\\SensitiveWordController');

});
