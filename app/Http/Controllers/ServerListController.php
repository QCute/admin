<?php

namespace App\Http\Controllers;

use App\Admin\Controllers\SwitchServerController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ServerListController extends Controller
{
    /**
     * @OA\Get(
     *     path = "/api/server-list",
     *     summary = "获取服务器列表",
     *     @OA\Parameter(
     *         description = "微信UnionId",
     *         in = "query",
     *         name = "unionId",
     *         required = true,
     *         @OA\Schema(type = "string"),
     *         @OA\Examples(example = "unionId", value = "oic_x5YDSA3gzvOFmf5E76", summary = "微信 Union Id."),
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example = "result", value = {{"server_name": "服务器名", "server_id": 1, "server_host": "127.0.0.1", "server_port": 8974}}, summary = "服务器列表"),
     *         )
     *     )
     * )
     */
    public function get(): JsonResponse
    {
        if (!env('API_SERVER_LIST', false)) {
            $list = DB::table('server_role_number')
                ->select(["server_name", "server_id", "server_host", "server_port"])
                ->get()
                ->toArray();
        } else {
            $sub = DB::table('server_role_number')
                ->select(DB::raw('MIN(`role_number`)'))
                ->toSql();
            $list = DB::table('server_role_number')
                ->select(["server_name", "server_id", "server_host", "server_port"])
                ->whereRaw("role_number = ( $sub )")
                ->limit(1)
                ->get()
                ->toArray();
        }
        return response()
            ->json($list)
            ->withHeaders([
                "Access-Control-Allow-Origin" => "*",
                "Access-Control-Max-Age" => "86400",
                "Access-Control-Allow-Headers" => "Content-Type, Accept, Authorization, X-Requested-With"
            ]);
    }

    public static function reload()
    {
        $list = SwitchServerController::getPublishServerList();
        $list = array_map(function ($row) { $row->role_number = 0; return (array)$row; }, $list);
        DB::table('server_role_number')->insertOrIgnore($list);
    }
}
