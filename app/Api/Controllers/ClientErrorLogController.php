<?php

namespace App\Api\Controllers;

use App\Api\Models\ClientErrorLogModel;
use Illuminate\Http\Request;

class ClientErrorLogController extends Controller
{
    public function report(Request $request)
    {
        // post must be carriage csrf field or remove VerifyCsrfToken
        $server_id = $request->json('server_id', 0) ?? 0;
        $account = $request->json('account', '') ?? '';
        $role_id = $request->json('role_id', 0) ?? 0;
        $role_name = $request->json('role_name', '') ?? '';
        $device = $request->json('device', '') ?? '';
        $env = $request->json('env', '') ?? '';
        $title = $request->json('title', '') ?? '';
        $content = $request->json('content', '') ?? '';
        $content_kernel = $request->json('content_kernel', '') ?? '';
        $ip = $request->getClientIp();
  
        // save
        $data = [
            'server_id' => $server_id, 
            'account' => $account, 
            'role_id' => $role_id, 
            'role_name' => $role_name, 
            'device' => $device, 
            'env' => $env, 
            'title' => $title, 
            'content' => $content, 
            'content_kernel' => $content_kernel, 
            'ip' => $ip, 
        ];

        ClientErrorLogModel::create($data);

        return ['result' => 'ok'];
    }
}
