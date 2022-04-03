<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Encore\Admin\Facades\Admin;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    'as' => config('admin.route.prefix') . '.',
    'domain' => config('admin.route.domain'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    // Switch Server Database
    $router->get('/switch-server', 'SwitchServerController@switch');

    // User Active Statistics
    $router->get('/user-online', 'ActiveStatisticsControllers\\UserOnlineController@index');
    $router->get('/user-register', 'ActiveStatisticsControllers\\UserRegisterController@index');
    $router->get('/user-login', 'ActiveStatisticsControllers\\UserLoginController@index');
    $router->get('/user-survival', 'ActiveStatisticsControllers\\UserSurvivalController@index');
    $router->get('/daily-online-time', 'ActiveStatisticsControllers\\DailyOnlineTimeController@index');

    // User Recharge Statistics
    $router->get('/daily-recharge', 'RechargeStatisticsControllers\\DailyRechargeController@index');
    $router->get('/recharge-rank', 'RechargeStatisticsControllers\\RechargeRankController@index');
    $router->get('/recharge-ratio', 'RechargeStatisticsControllers\\RechargeRatioController@index');
    $router->get('/recharge-distribution', 'RechargeStatisticsControllers\\RechargeDistributionController@index');
    $router->get('/first-recharge-time-distribution', 'RechargeStatisticsControllers\\FirstRechargeTimeDistributionController@index');

    // Game Data(user/configure/log)
    $router->get('/user-data', 'GameDataControllers\\TableDataListController@index');
    $router->get('/configure-data', 'GameDataControllers\\TableDataListController@index');
    $router->get('/log-data', 'GameDataControllers\\TableDataListController@index');
    $router->get('/table-data-viewer', 'GameDataControllers\\TableDataViewerController@index');
    $router->resource('/client-error-log', 'GameDataControllers\\ClientErrorLogController');

    // Configure Data
    $router->get('/configure-table', 'GameConfigureControllers\\ConfigureTableController@index');
    $router->post('/configure-table', 'GameConfigureControllers\\ConfigureTableController@index');
    $router->get('/configure-table-download', 'GameConfigureControllers\\ConfigureTableController@download');
    $router->get('/erl-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->get('/erl-configure-download', 'GameConfigureControllers\\ConfigureListController@download');
    $router->get('/lua-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->get('/lua-configure-download', 'GameConfigureControllers\\ConfigureListController@download');
    $router->get('/js-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->get('/js-configure-download', 'GameConfigureControllers\\ConfigureListController@download');

    // Server Manage
    $router->resource('/server-list', 'ServerManageControllers\\ServerListController');
    $router->get('/server-tuning', 'ServerManageControllers\\ServerTuningController@index');
    $router->get('/server-tuning-get-server-time', 'ServerManageControllers\\ServerTuningController@getServerTime');
    $router->get('/server-tuning-get-server-state', 'ServerManageControllers\\ServerTuningController@getServerState');
    $router->get('/open-server', 'ServerManageControllers\\OpenServerController@index');
    $router->post('/open-server', 'ServerManageControllers\\OpenServerController@index');
    $router->get('/merge-server', 'ServerManageControllers\\MergeServerController@index');
    $router->post('/merge-server', 'ServerManageControllers\\MergeServerController@index');

    // Operation
    $router->get('/user-manage', 'OperationControllers\\UserManageController@index');
    $router->post('/user-manage', 'OperationControllers\\UserManageController@index');
    $router->get('/game-mail', 'OperationControllers\\GameMailController@index');
    $router->post('/game-mail', 'OperationControllers\\GameMailController@index');
    $router->get('/game-notice', 'OperationControllers\\GameNoticeController@index');
    $router->post('/game-notice', 'OperationControllers\\GameNoticeController@index');
    $router->resource('/maintain-notice', 'OperationControllers\\MaintainNoticeController');
    $router->resource('/impeach', 'OperationControllers\\ImpeachController');
    $router->resource('/sensitive-word', 'OperationControllers\\SensitiveWordController');

    // Assistant
    $router->get('/key-assistant', 'AssistantControllers\\KeyAssistantController@index');
    $router->post('/key-assistant', 'AssistantControllers\\KeyAssistantController@index');
    $router->get('/configure-assistant', 'AssistantControllers\\ConfigureAssistantController@index');
    $router->post('/configure-assistant', 'AssistantControllers\\ConfigureAssistantController@index');

});
