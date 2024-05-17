<?php

namespace App\Admin\Controllers\Extend;

use App\Admin\Controllers\Controller;
use App\Admin\Services\Extend\DatabaseService;
use App\Admin\Services\Extend\ServerService;

class ServerController extends Controller
{
    public function index()
    {
        return ServerService::getServers();
    }

    public function change()
    {
        $id = request()->input('server');

        $server = ServerService::getServer($id);

        if(is_null($server)) {
            return [
                'code' => 403, 
                'msg' => trans('admin.server_not_found')
            ];
        }

        ServerService::saveServer($id);

        $server->tag === '' ? DatabaseService::changeConnection($server) : NULL;

        return ['code' => 0, 'msg' => ''];
    }
}
