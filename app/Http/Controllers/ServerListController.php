<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\SwitchServerController;
use Illuminate\Http\JsonResponse;

class ServerListController extends Controller
{
    public function get(): JsonResponse
    {
        return response()
            ->json(SwitchServerController::getPublishServerList())
            ->withHeaders([
                "Access-Control-Allow-Origin" => "*",
                "Access-Control-Max-Age" => "86400",
                "Access-Control-Allow-Headers" => "Content-Type, Accept, Authorization, X-Requested-With"
            ]);
    }
}
