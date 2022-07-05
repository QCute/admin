<?php

use App\Http\Controllers\ClientErrorLogReportController;
use App\Http\Controllers\ImpeachReportController;
use App\Http\Controllers\MaintainNoticeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServerListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// server list
Route::get("/server-list", [ServerListController::class, "get"]);
// notice
Route::get("/maintain-notice", [MaintainNoticeController::class, "get"]);
// impeach
Route::post("/impeach", [ImpeachReportController::class, "report"]);
// client error log
Route::post("/client-error-log", [ClientErrorLogReportController::class, "report"]);
// payment
Route::get("/payment", [PaymentController::class, "pay"]);
