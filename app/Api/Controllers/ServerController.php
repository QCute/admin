<?php

namespace App\Api\Controllers;

use App\Api\Models\ServerModel;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function get(Request $request)
    {
        $headers = [
            "Access-Control-Allow-Origin" => "*",
            "Access-Control-Max-Age" => "86400",
            "Access-Control-Allow-Headers" => "Content-Type, Accept, Authorization, X-Requested-With"
        ];
        
        foreach($headers as $key => $value) {
            header("$key: $value");
        }
 
        $channel = $request->input('channel', '');

        $model = new ServerModel();
        return $model
            ->where('channel_id', $channel)
            ->orWhere('channel_name', $channel)
            ->orderBy('number')
            ->limit(1)
            ->first([
                'channel_id',
                'channel_name',
                'server_id', 
                'server_name', 
                'server_protocol',
                'server_host', 
                'server_port', 
            ]);
    }

    public function list(Request $request)
    {
        $channel = $request->input('channel', '');

        $per = $request->input('per', 10);
        
        $page = $request->input('page', 0);

        $model = new ServerModel();
        $data = $model
            ->where('channel_id', $channel)
            ->orWhere('channel_name', $channel)
            ->simplePaginate($per, [
                'channel_id',
                'channel_name',
                'server_id', 
                'server_name', 
                'server_protocol',
                'server_host', 
                'server_port', 
            ], 'page', $page)
            ->items();

        $headers = [
            "Access-Control-Allow-Origin" => "*",
            "Access-Control-Max-Age" => "86400",
            "Access-Control-Allow-Headers" => "Content-Type, Accept, Authorization, X-Requested-With"
        ];
        
        foreach($headers as $key => $value) {
            header("$key: $value");
        }

        return $data;
    }
}
