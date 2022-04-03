<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ClientErrorLogReportController extends Controller
{
    /**
     * @OA\Post(
     *     path = "/api/client-error-log",
     *     summary = "客户端错误日志上报",
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
     *                     property = "account",
     *                     type = "string",
     *                     description = "账户名",
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
     *                     property = "device",
     *                     type = "string",
     *                     description = "设备",
     *                 ),
     *                 @OA\Property(
     *                     property = "env",
     *                     type = "string",
     *                     description = "环境",
     *                 ),
     *                 @OA\Property(
     *                     property = "title",
     *                     type = "string",
     *                     description = "标题",
     *                 ),
     *                 @OA\Property(
     *                     property = "content",
     *                     type = "string",
     *                     description = "内容",
     *                 ),
     *                 @OA\Property(
     *                     property = "content_kernel",
     *                     type = "string",
     *                     description = "内容核心",
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
        $account = request()->input("account", "");
        $role_id = request()->input("role_id", 0);
        $role_name = request()->input("role_name", "");
        $device = request()->input("device", "");
        $env = request()->input("env", "");
        $title = request()->input("title", "");
        $content = request()->input("content", "");
        $content_kernel = request()->input("content_kernel", "");
        $ip = request()->ip();
        $time = now();
        // save
        $data = ["server_id" => $server_id, "account" => $account, "role_id" => $role_id, "role_name" => $role_name, "device" => $device, "env" => $env, "title" => $title, "content" => $content, "content_kernel" => $content_kernel, "ip" => $ip, "time" => $time];
        DB::insert("INSERT INTO `client_error_log` (`server_id`, `account`, `role_id`, `role_name`, `device`, `env`, `title`, `content`, `content_kernel`, `ip`, `time`) VALUES (:server_id, :account, :role_id, :role_name, :device, :env, :title, :content, :content_kernel, :ip, :time)", $data);
        return response()->json(["result" => "ok"]);
    }
}
