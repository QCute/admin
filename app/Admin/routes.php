<?php

use Encore\Admin\Facades\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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
    $router->get('/switch/channel', 'SwitchServerController@switchChannel');
    $router->get('/switch/node', 'SwitchServerController@switchNode');
    $router->get('/reload/server', 'SwitchServerController@reloadServer');

    // User Active Statistics
    $router->get('/active-statistics/user-online', 'ActiveStatisticsControllers\\UserOnlineController@index');
    $router->get('/active-statistics/user-register', 'ActiveStatisticsControllers\\UserRegisterController@index');
    $router->get('/active-statistics/user-login', 'ActiveStatisticsControllers\\UserLoginController@index');
    $router->get('/active-statistics/user-survival', 'ActiveStatisticsControllers\\UserSurvivalController@index');
    $router->get('/active-statistics/daily-online-time', 'ActiveStatisticsControllers\\DailyOnlineTimeController@index');

    // User Charge Statistics
    $router->get('/charge-statistics/ltv', 'ChargeStatisticsControllers\\LtvController@index');
    $router->get('/charge-statistics/arp-u', 'ChargeStatisticsControllers\\ArpUController@index');
    $router->get('/charge-statistics/arp-pu', 'ChargeStatisticsControllers\\ArpPuController@index');
    $router->get('/charge-statistics/charge-rate', 'ChargeStatisticsControllers\\ChargeRateController@index');
    $router->get('/charge-statistics/daily-charge', 'ChargeStatisticsControllers\\DailyChargeController@index');
    $router->get('/charge-statistics/charge-rank', 'ChargeStatisticsControllers\\ChargeRankController@index');
    $router->get('/charge-statistics/charge-ratio', 'ChargeStatisticsControllers\\ChargeRatioController@index');
    $router->get('/charge-statistics/charge-distribution', 'ChargeStatisticsControllers\\ChargeDistributionController@index');
    $router->get('/charge-statistics/first-charge-time-distribution', 'ChargeStatisticsControllers\\FirstChargeTimeDistributionController@index');

    // User Statistics
    $router->get('/statistics/level', 'StatisticsControllers\\LevelController@index');
    $router->get('/statistics/asset-produce', 'StatisticsControllers\\AssetProduceController@index');
    $router->get('/statistics/asset-consume', 'StatisticsControllers\\AssetConsumeController@index');

    // Game Data(user/configure/log)
    $router->get('/game-data/user-data', 'GameDataControllers\\TableDataListController@index');
    $router->get('/game-data/configure-data', 'GameDataControllers\\TableDataListController@index');
    $router->get('/game-data/log-data', 'GameDataControllers\\TableDataListController@index');
    $router->get('/game-data/table-data-viewer', 'GameDataControllers\\TableDataViewerController@index');
    $router->resource('/game-data/client-error-log', 'GameDataControllers\\ClientErrorLogController');

    // Configure Data
    $router->get('/configure-data/configure-table', 'GameConfigureControllers\\ConfigureTableController@index');
    $router->post('/configure-data/configure-table', 'GameConfigureControllers\\ConfigureTableController@index');
    $router->get('/configure-data/configure-table-export', 'GameConfigureControllers\\ConfigureTableController@export');
    $router->get('/configure-data/configure-table-download', 'GameConfigureControllers\\ConfigureTableController@download');
    $router->get('/configure-data/erl-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->post('/configure-data/erl-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->get('/configure-data/erl-configure-export', 'GameConfigureControllers\\ConfigureListController@export');
    $router->get('/configure-data/erl-configure-download', 'GameConfigureControllers\\ConfigureListController@download');
    $router->get('/configure-data/lua-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->post('/configure-data/lua-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->get('/configure-data/lua-configure-export', 'GameConfigureControllers\\ConfigureListController@export');
    $router->get('/configure-data/lua-configure-download', 'GameConfigureControllers\\ConfigureListController@download');
    $router->get('/configure-data/js-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->post('/configure-data/js-configure', 'GameConfigureControllers\\ConfigureListController@index');
    $router->get('/configure-data/js-configure-export', 'GameConfigureControllers\\ConfigureListController@export');
    $router->get('/configure-data/js-configure-download', 'GameConfigureControllers\\ConfigureListController@download');

    // Server Manage
    $router->resource('/server-manage/server-list', 'ServerManageControllers\\ServerListController');
    $router->get('/server-manage/server-tuning', 'ServerManageControllers\\ServerTuningController@index');
    $router->get('/server-manage/server-tuning-get-server-time', 'ServerManageControllers\\ServerTuningController@getServerTime');
    $router->get('/server-manage/server-tuning-get-server-state', 'ServerManageControllers\\ServerTuningController@getServerState');
    $router->get('/server-manage/open-server', 'ServerManageControllers\\OpenServerController@index');
    $router->post('/server-manage/open-server', 'ServerManageControllers\\OpenServerController@index');
    $router->get('/server-manage/merge-server', 'ServerManageControllers\\MergeServerController@index');
    $router->post('/server-manage/merge-server', 'ServerManageControllers\\MergeServerController@index');

    // Operation
    $router->get('/operation/user-manage', 'OperationControllers\\UserManageController@index');
    $router->post('/operation/user-manage', 'OperationControllers\\UserManageController@index');
    $router->get('/operation/game-mail', 'OperationControllers\\GameMailController@index');
    $router->post('/operation/game-mail', 'OperationControllers\\GameMailController@index');
    $router->get('/operation/game-notice', 'OperationControllers\\GameNoticeController@index');
    $router->post('/operation/game-notice', 'OperationControllers\\GameNoticeController@index');
    $router->resource('/operation/maintain-notice', 'OperationControllers\\MaintainNoticeController');
    $router->resource('/operation/impeach', 'OperationControllers\\ImpeachController');
    $router->resource('/operation/sensitive-word', 'OperationControllers\\SensitiveWordController');

    // Assistant
    $router->get('/assistant/key-assistant', 'AssistantControllers\\KeyAssistantController@index');
    $router->post('/assistant/key-assistant', 'AssistantControllers\\KeyAssistantController@index');
    $router->get('/assistant/configure-assistant', 'AssistantControllers\\ConfigureAssistantController@index');
    $router->post('/assistant/configure-assistant', 'AssistantControllers\\ConfigureAssistantController@index');
    $router->get('/assistant/subversion-assistant', 'AssistantControllers\\SubversionAssistantController@index');
    $router->post('/assistant/subversion-assistant', 'AssistantControllers\\SubversionAssistantController@index');

});
