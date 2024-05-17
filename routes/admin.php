<?php

use App\Admin\Controllers\ActiveStatistic\DailyOnlineTimeController;
use App\Admin\Controllers\ActiveStatistic\UserLoginController;
use App\Admin\Controllers\ActiveStatistic\UserOnlineController;
use App\Admin\Controllers\ActiveStatistic\UserRegisterController;
use App\Admin\Controllers\ActiveStatistic\UserSurvivalController;
use App\Admin\Controllers\Assistant\ChartBuilderTestController;
use App\Admin\Controllers\Assistant\FormBuilderTestController;
use App\Admin\Controllers\Assistant\TableBuilderTestController;
use App\Admin\Controllers\Assistant\KeyAssistantController;
use App\Admin\Controllers\Auth\AuthController;
use App\Admin\Controllers\Admin\MenuController;
use App\Admin\Controllers\Admin\OperationLogController;
use App\Admin\Controllers\Admin\PermissionController;
use App\Admin\Controllers\Admin\RoleController;
use App\Admin\Controllers\Admin\SettingController;
use App\Admin\Controllers\Admin\UserController;
use App\Admin\Controllers\ChargeStatistic\ArpPuController;
use App\Admin\Controllers\ChargeStatistic\ArpUController;
use App\Admin\Controllers\ChargeStatistic\ChargeDailyController;
use App\Admin\Controllers\ChargeStatistic\ChargeDistributionController;
use App\Admin\Controllers\ChargeStatistic\ChargeRankController;
use App\Admin\Controllers\ChargeStatistic\ChargeRateController;
use App\Admin\Controllers\ChargeStatistic\FirstChargeTimeDistributionController;
use App\Admin\Controllers\ChargeStatistic\LtvController;
use App\Admin\Controllers\Extend\ChannelController;
use App\Admin\Controllers\Extend\IndexController;
use App\Admin\Controllers\Extend\ServerController;
use App\Admin\Controllers\GameConfigure\ConfigureFileController;
use App\Admin\Controllers\GameConfigure\ConfigureTableController;
use App\Admin\Controllers\GameData\ClientErrorLogController;
use App\Admin\Controllers\GameData\TableDataController;
use App\Admin\Controllers\GameData\TableListController;
use App\Admin\Controllers\HomeController;
use App\Admin\Controllers\Operation\MailController;
use App\Admin\Controllers\Operation\NoticeController;
use App\Admin\Controllers\Operation\ImpeachController;
use App\Admin\Controllers\Operation\MaintainNoticeController;
use App\Admin\Controllers\Operation\SensitiveWordDataController;
use App\Admin\Controllers\Operation\UserChatManageController;
use App\Admin\Controllers\Operation\UserManageController;
use App\Admin\Controllers\ServerManage\ChannelListController;
use App\Admin\Controllers\ServerManage\MergeServerController;
use App\Admin\Controllers\ServerManage\OpenServerController;
use App\Admin\Controllers\ServerManage\ServerListController;
use App\Admin\Controllers\ServerManage\ServerTuningController;
use App\Admin\Controllers\Statistic\AssetConsumeController;
use App\Admin\Controllers\Statistic\AssetProduceController;
use App\Admin\Controllers\Statistic\LevelController;
use Illuminate\Support\Facades\Route;

// admin
Route::get('/auth/index', [AuthController::class, 'index']);
Route::get('/auth/captcha', [AuthController::class, 'captcha']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/profile', [AuthController::class, 'profile']);
Route::get('/auth/logout', [AuthController::class, 'logout']);

// admin setting
Route::get('/auth/setting', [SettingController::class, 'get']);
Route::post('/auth/setting', [SettingController::class, 'set']);

// admin user/role/permission
Route::resource('/admin/user', UserController::class);
Route::resource('/admin/role', RoleController::class);
Route::resource('/admin/permission', PermissionController::class);
Route::resource('/admin/menu', MenuController::class);
Route::resource('/admin/log', OperationLogController::class);

// config
Route::get('/config', [IndexController::class, 'config']);
// menu
Route::get('/menu', [IndexController::class, 'menu']);
// refresh
Route::post('/refresh', [IndexController::class, 'refresh']);

// admin extend channel/server
Route::get('/channel/get', [ChannelController::class, 'index']);
Route::post('/channel/change', [ChannelController::class, 'change']);
Route::get('/server/get', [ServerController::class, 'index']);
Route::post('/server/change', [ServerController::class, 'change']);

// Home
Route::get('/', [HomeController::class, 'index']);

// User Active Statistic
Route::get('/active-statistic/user-online', [UserOnlineController::class, 'index']);
Route::get('/active-statistic/user-register', [UserRegisterController::class, 'index']);
Route::get('/active-statistic/user-login', [UserLoginController::class, 'index']);
Route::get('/active-statistic/user-survival', [UserSurvivalController::class, 'index']);
Route::get('/active-statistic/daily-online-time', [DailyOnlineTimeController::class, 'index']);

// User Charge Statistic
Route::get('/charge-statistic/ltv', [LtvController::class, 'index']);
Route::get('/charge-statistic/arp-u', [ArpUController::class, 'index']);
Route::get('/charge-statistic/arp-pu', [ArpPuController::class, 'index']);
Route::get('/charge-statistic/charge-rate', [ChargeRateController::class, 'index']);
Route::get('/charge-statistic/charge-daily', [ChargeDailyController::class, 'index']);
Route::get('/charge-statistic/charge-rank', [ChargeRankController::class, 'index']);
Route::get('/charge-statistic/charge-distribution', [ChargeDistributionController::class, 'index']);
Route::get('/charge-statistic/first-charge-time-distribution', [FirstChargeTimeDistributionController::class, 'index']);

// User Statistic
Route::get('/statistic/level', [LevelController::class, 'index']);
Route::get('/statistic/asset-produce', [AssetProduceController::class, 'index']);
Route::get('/statistic/asset-consume', [AssetConsumeController::class, 'index']);

// Game Data(user/configure/log)
Route::get('/game-table/{type}/{action?}', [TableListController::class, 'index']);
Route::get('/game-data/{table}/{action?}', [TableDataController::class, 'index']);
Route::resource('/game-admin/client-error-log', ClientErrorLogController::class);

// Configure Data
Route::get('/configure-table', [ConfigureTableController::class, 'index']);
Route::get('/configure-file/erl', [ConfigureFileController::class, 'index']);
Route::get('/configure-file/lua', [ConfigureFileController::class, 'index']);
Route::get('/configure-file/js', [ConfigureFileController::class, 'index']);

// Server Manage
Route::resource('/server-manage/channel-list', ChannelListController::class);
Route::resource('/server-manage/server-list', ServerListController::class);
Route::get('/server-manage/server-tuning/index', [ServerTuningController::class, 'index']);
Route::get('/server-manage/server-tuning/get-server-time', [ServerTuningController::class, 'getServerTime']);
Route::get('/server-manage/server-tuning/get-server-open-time', [ServerTuningController::class, 'getServerOpenTime']);
Route::get('/server-manage/server-tuning/get-server-state', [ServerTuningController::class, 'getServerState']);
Route::get('/server-manage/open', [OpenServerController::class, 'index']);
Route::post('/server-manage/open-server', [OpenServerController::class, 'submit']);
Route::get('/server-manage/merge', [MergeServerController::class, 'index']);
Route::post('/server-manage/merge-submit', [MergeServerController::class, 'submit']);

// Operation
Route::resource('/operation/user-manage', UserManageController::class);
Route::resource('/operation/user-chat-manage', UserChatManageController::class);
Route::resource('/operation/mail', MailController::class);
Route::resource('/operation/notice', NoticeController::class);
Route::resource('/operation/maintain-notice', MaintainNoticeController::class);
Route::resource('/operation/impeach', ImpeachController::class);
Route::resource('/operation/sensitive-word-data', SensitiveWordDataController::class);

// Assistant
Route::get('/assistant/key-assistant', [KeyAssistantController::class, 'index']);
Route::post('/assistant/key-assistant-make', [KeyAssistantController::class, 'make']);
Route::resource('/assistant/chart-builder-test', ChartBuilderTestController::class);
Route::resource('/assistant/form-builder-test', FormBuilderTestController::class);
Route::resource('/assistant/table-builder-test', TableBuilderTestController::class);