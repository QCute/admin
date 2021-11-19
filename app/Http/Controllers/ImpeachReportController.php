<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ImpeachReportController extends Controller
{
    public function report(): JsonResponse
    {
        // post must be carriage csrf field or remove VerifyCsrfToken
        $server_id = request()->query("server_id", 0);
        $role_id = request()->query("role_id", 0);
        $role_name = request()->query("role_name", "");
        $impeacher_server_id = request()->query("impeacher_server_id", 0);
        $impeacher_role_id = request()->query("impeacher_role_id", 0);
        $impeacher_role_name = request()->query("impeacher_role_name", "");
        $type = request()->query("type", "");
        $content = request()->query("content", "");
        $ip = request()->ip();
        $time = now();
        // save
        $data = ["server_id" => $server_id, "role_id" => $role_id, "role_name" => $role_name, "impeacher_server_id" => $impeacher_server_id, "impeacher_role_id" => $impeacher_role_id, "impeacher_role_name" => $impeacher_role_name, "type" => $type, "content" => $content, "ip" => $ip, "time" => $time];
        DB::insert("INSERT INTO `impeach` (`server_id`, `role_id`, `role_name`, `impeacher_server_id`, `impeacher_role_id`, `impeacher_role_name`, `type`, `content`, `ip`, `time`) VALUES (:server_id, :role_id, :role_name, :impeacher_server_id, :impeacher_role_id, :impeacher_role_name, :type, :content, :ip, :time)", $data);
        return response()->json(["result" => "ok"]);
    }
}
