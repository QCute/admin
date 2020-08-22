<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ClientErrorLogReportController extends Controller
{
    public function report()
    {
        // body json
        // post must be carriage csrf field or remove VerifyCsrfToken
        $data = request()->all();
        DB::insert("INSERT INTO `client_error_log` (`server_id`, `account`, `role_id`, `role_name`, `env`, `title`, `content`, `content_kernel`, `ip`, `time`) VALUES (" . $data["server_id"] . ", '" . $data["account"] . "', " . $data["role_id"] . ", '" . $data["role_name"] . "', '" . $data["env"] . "', '" . $data["title"] . "', '" . $data["content"] . "', '" . $data["content_kernel"] . "', '" . $data["ip"] . "', " . time() . ")");
        return json_encode($data);
    }
}
