<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClientErrorLogReportController;
use App\Http\Controllers\ImpeachReportController;
use App\Http\Controllers\MaintainNoticeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServerListController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// web
Route::domain(env("APP_URL"))->group(function () {
    Route::get("", function () { return view('welcome'); });
});

// api
Route::domain(env("API_DOMAIN", "api" . "." . env("APP_URL")))->group(function () {
    // server list
    Route::get("/server-list", [ServerListController::class, "get"]);
    // post csrf token
    Route::get("/csrf-token", function() { return response()->json(["_token" => csrf_token()]); });
    // notice
    Route::get("/maintain-notice", [MaintainNoticeController::class, "get"]);
    // impeach
    Route::get("/impeach", [ImpeachReportController::class, "report"]);
    // client error log
    Route::get("/client-error-log", [ClientErrorLogReportController::class, "report"]);
    // payment
    Route::get("/payment", [PaymentController::class, "pay"]);
});
