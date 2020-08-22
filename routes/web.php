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

// web
Route::domain("www" . "." . env("APP_URL"))->group(function () {
    Route::get("", function () { return view('welcome'); });
});

// api
Route::domain(env("API_ROUTE_DOMAIN", "api" . "." . env("APP_URL")))->group(function () {
    // server list
    Route::get("/", function () {
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        return json_encode(SwitchServerController::getPublicServerList());
    });

    // client error report
    Route::get("/error", "ClientErrorLogReportController@report");
});

// payment api
Route::domain(env("PAYMENT_ROUTE_DOMAIN", "payment" . "." . env("APP_URL")))->group(function () {
    Route::get("", "PaymentController@pay");
    Route::post("", "PaymentController@pay");
});
