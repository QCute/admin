<?php

use Illuminate\Support\Facades\Route;
use App\Admin\Controllers\SwitchServerController;

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
    Route::get("/server-list", function () {
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        return json_encode(SwitchServerController::getPublishServerList());
    });
    // post csrf token
    Route::get("/csrf-token", function (){ return json_encode(["_token" => csrf_token()]); });
    // notice
    Route::get("/maintain-notice", "MaintainNoticeController@get");
    // impeach
    Route::get("/impeach", "ImpeachReportController@report");
    // client error log
    Route::get("/client-error-log", "ClientErrorLogReportController@report");
    // payment
    Route::get("/payment", "PaymentController@pay");
});
