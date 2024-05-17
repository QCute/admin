<?php

namespace App\Api\Controllers;

use App\Api\Models\ImpeachModel;
use Illuminate\Http\Request;

class ImpeachController extends Controller
{
    public function report(Request $request)
    {
        // post must be carriage csrf field or remove VerifyCsrfToken
        $server_id = $request->json('server_id', 0);
        $role_id = $request->json('role_id', 0);
        $role_name = $request->json('role_name', '');
        $impeacher_server_id = $request->json('impeacher_server_id', 0);
        $impeacher_role_id = $request->json('impeacher_role_id', 0);
        $impeacher_role_name = $request->json('impeacher_role_name', '');
        $type = $request->json('type', '');
        $content = $request->json('content', '');
        $ip = $request->ip();

        // save
        $data = [
            'server_id' => $server_id, 
            'role_id' => $role_id, 
            'role_name' => $role_name, 
            'impeacher_server_id' => $impeacher_server_id, 
            'impeacher_role_id' => $impeacher_role_id, 
            'impeacher_role_name' => $impeacher_role_name, 
            'type' => $type, 
            'content' => $content, 
            'ip' => $ip, 
        ];
        $model = new ImpeachModel();
        $model->insert($data);
        
        return ['result' => 'ok'];
    }
}
