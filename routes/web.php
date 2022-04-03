<?php

use Illuminate\Support\Facades\DB;
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
    Route::get("", function () { return view('home'); });
});

// api
Route::group([
    'prefix' => env('API_ROUTE_PREFIX'),
    'domain' => env('API_ROUTE_DOMAIN'),
], function () {
    // set server role
    Route::get("/set-server-role", function() {
        $bindings = [
            request()->input("account_name", ""),
            request()->input("role_id", ""),
            request()->input("server_id", "")
        ];
        DB::insert("INSERT IGNORE `server_role` VALUES (?, ?, ?)", $bindings);
    });
    // delete server role
    Route::get("/delete-server-role", function() {
        DB::table("server_role")
            ->where("account_name", request()->input("account_name", ""))
            ->delete();
    });
    // server list
    Route::get("/server-list", [ServerListController::class, "get"]);
    // post csrf token
    Route::get("/csrf-token", function() { return response()->json(["_token" => csrf_token()]); });
    // notice
    Route::get("/maintain-notice", [MaintainNoticeController::class, "get"]);
    // impeach
    Route::post("/impeach", [ImpeachReportController::class, "report"]);
    // client error log
    Route::post("/client-error-log", [ClientErrorLogReportController::class, "report"]);
    // payment
    Route::get("/payment", [PaymentController::class, "pay"]);
});
