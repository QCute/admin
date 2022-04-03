<?php

namespace App\Http\Controllers;

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
     *         in = "path",
     *         name = "unionId",
     *         required = true,
     *         @OA\Schema(type = "string"),
     *         @OA\Examples(example = "int", value = "oic_x5YDSA3gzvOFmf5E76", summary = "Union Id."),
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
        $unionId = request()->input("unionId", "");
        $serverList = DB::table("server_list")
            ->select([
                "server_list.server_name",
                "server_list.server_id",
                "server_list.server_host",
                "server_list.server_port",
            ])
            ->leftJoin("server_role", "server_list.server_id", "=", "server_role.server_id")
            ->where("server_list.server_type", "local")
            ->where("server_role.account_name", "=", $unionId)
            ->get()
            ->toArray();
        // server role not set
        if(empty($serverList)) {
            // select the min role number server
            $server = DB::table("server_role")
                ->select(DB::raw("server_list.server_name, server_list.server_id, server_list.server_host, server_list.server_port, count(`account_name`) AS `count`"))
                ->rightJoin("server_list", "server_list.server_id", "=", "server_role.server_id")
                ->groupBy("server_role.server_id")
                ->orderBy("count", "ASC")
                ->first();
            unset($server->count);
            // save role data
            DB::insert("INSERT IGNORE `server_role` VALUES (?, ?, ?)", [$unionId, 0, $server->server_id]);
            // set to list
            $serverList = [$server];
        }
        return response()
            ->json($serverList)
            ->withHeaders([
                "Access-Control-Allow-Origin" => "*",
                "Access-Control-Max-Age" => "86400",
                "Access-Control-Allow-Headers" => "Content-Type, Accept, Authorization, X-Requested-With"
            ]);
    }
}
