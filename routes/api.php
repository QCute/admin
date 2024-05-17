<?php

use App\Api\Controllers\ChargeOrderController;
use App\Api\Controllers\ClientErrorLogController;
use App\Api\Controllers\FeedbackController;
use App\Api\Controllers\ImpeachController;
use App\Api\Controllers\MaintainNoticeController;
use App\Api\Controllers\MiniGameController;
use App\Api\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

// client error log
Route::post("/client-error-log", [ClientErrorLogController::class, "report"]);

// charge notify
Route::post("/charge", [ChargeOrderController::class, "notify"]);

// impeach
Route::post("/impeach", [ImpeachController::class, "report"]);

// feedback
Route::post("/feedback", [FeedbackController::class, "report"]);

// get notice
Route::get("/maintain-notice", [MaintainNoticeController::class, "get"]);

// get server
Route::get("/server", [ServerController::class, "get"]);

// get server list
Route::get("/server-list", [ServerController::class, "list"]);

// WeChat Mini Game login
Route::get("/WeChatLogin", [MiniGameController::class, "weChatLogin"]);

// TikTok Mini Game login
Route::get("/TikTokLogin", [MiniGameController::class, "tikTokLogin"]);
