<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ClientErrorLogReportController extends Controller
{
    public function report()
    {
        // post must be carriage csrf field or remove VerifyCsrfToken
        $server_id = request()->query("server_id", 0);
        $account = request()->query("account", "");
        $role_id = request()->query("role_id", 0);
        $role_name = request()->query("role_name", "");
        $device = request()->query("device", "");
        $env = request()->query("env", "");
        $title = request()->query("title", "");
        $content = request()->query("content", "");
        $content_kernel = request()->query("content_kernel", "");
        $ip = request()->ip();
        $time = now();
        // save
        $data = ["server_id" => $server_id, "account" => $account, "role_id" => $role_id, "role_name" => $role_name, "device" => $device, "env" => $env, "title" => $title, "content" => $content, "content_kernel" => $content_kernel, "ip" => $ip, "time" => $time];
        DB::insert("INSERT INTO `client_error_log` (`server_id`, `account`, `role_id`, `role_name`, `device`, `env`, `title`, `content`, `content_kernel`, `ip`, `time`) VALUES (:server_id, :account, :role_id, :role_name, :device, :env, :title, :content, :content_kernel, :ip, :time)", $data);
        return json_encode(["result" => "ok"]);
    }
}
