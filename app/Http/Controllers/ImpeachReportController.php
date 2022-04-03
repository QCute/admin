<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ImpeachReportController extends Controller
{
    /**
     * @OA\Post(
     *     path = "/api/impeach",
     *     summary = "举报",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property = "server_id",
     *                     type = "integer",
     *                     description = "服务器ID",
     *                 ),
     *                 @OA\Property(
     *                     property = "role_id",
     *                     type = "integer",
     *                     description = "角色ID",
     *                 ),
     *                 @OA\Property(
     *                     property = "role_name",
     *                     type = "string",
     *                     description = "角色名",
     *                 ),
     *                 @OA\Property(
     *                     property = "impeacher_server_id",
     *                     type = "integer",
     *                     description = "举报者服务器ID",
     *                 ),
     *                 @OA\Property(
     *                     property = "impeacher_role_id",
     *                     type = "integer",
     *                     description = "举报者角色ID",
     *                 ),
     *                 @OA\Property(
     *                     property = "impeacher_role_name",
     *                     type = "string",
     *                     description = "举报者角色名",
     *                 ),
     *                 @OA\Property(
     *                     property = "type",
     *                     type = "string",
     *                     description = "类型",
     *                 ),
     *                 @OA\Property(
     *                     property = "content",
     *                     type = "string",
     *                     description = "内容",
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "OK",
     *         @OA\JsonContent(
     *             @OA\Examples(example = "result", value = {"result": "ok"}, summary = "结果"),
     *         )
     *     )
     * )
     */
    public function report(): JsonResponse
    {
        // post must be carriage csrf field or remove VerifyCsrfToken
        $server_id = request()->input("server_id", 0);
        $role_id = request()->input("role_id", 0);
        $role_name = request()->input("role_name", "");
        $impeacher_server_id = request()->input("impeacher_server_id", 0);
        $impeacher_role_id = request()->input("impeacher_role_id", 0);
        $impeacher_role_name = request()->input("impeacher_role_name", "");
        $type = request()->input("type", "");
        $content = request()->input("content", "");
        $ip = request()->ip();
        $time = now();
        // save
        $data = ["server_id" => $server_id, "role_id" => $role_id, "role_name" => $role_name, "impeacher_server_id" => $impeacher_server_id, "impeacher_role_id" => $impeacher_role_id, "impeacher_role_name" => $impeacher_role_name, "type" => $type, "content" => $content, "ip" => $ip, "time" => $time];
        DB::insert("INSERT INTO `impeach` (`server_id`, `role_id`, `role_name`, `impeacher_server_id`, `impeacher_role_id`, `impeacher_role_name`, `type`, `content`, `ip`, `time`) VALUES (:server_id, :role_id, :role_name, :impeacher_server_id, :impeacher_role_id, :impeacher_role_name, :type, :content, :ip, :time)", $data);
        return response()->json(["result" => "ok"]);
    }
}
