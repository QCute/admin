<?php

namespace App\Api\Controllers;

use App\Api\Models\FeedbackModel;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function report(Request $request)
    {
        // post must be carriage csrf field or remove VerifyCsrfToken
        $server_id = $request->json('server_id', 0);
        $role_id = $request->json('role_id', 0);
        $role_name = $request->json('role_name', '');
        $content = $request->json('content', '');
        $ip = $request->ip();

        // save
        $data = [
            'server_id' => $server_id, 
            'role_id' => $role_id, 
            'role_name' => $role_name, 
            'content' => $content, 
            'ip' => $ip, 
        ];
        $model = new FeedbackModel();
        $model->insert($data);
        
        return ['result' => 'ok'];
    }
}
