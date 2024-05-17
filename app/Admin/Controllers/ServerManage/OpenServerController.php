<?php

namespace App\Admin\Controllers\ServerManage;

use App\Admin\Builders\Form;
use App\Admin\Controllers\Controller;
use App\Admin\Services\Extend\MachineService;
use App\Admin\Services\Extend\ServerService;
use Illuminate\Http\Request;

class OpenServerController extends Controller
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->name(trans('admin.server.open'));

        // machine
        $cfg = MachineService::getSSHConfig();
        $select = $form->select('machine')->label(trans('admin.machine'))->required();
        foreach($cfg as $host => $machine) {
            $select->option()->label($host)->value($host);
        }

        // passowrd
        $form->password('password')->label(trans('admin.form.password'))->required();

        // server
        $select = $form->select('server')->label(trans('admin.channel'))->required();
        $servers = ServerService::getServers();
        foreach($servers as $server) {
            if($server->type != 'local') continue;
            $select->option()->label($server->name)->value($server->id);
        }

        return $form->build();
    }

    public function submit(Request $request)
    {
        $cfg = MachineService::getSSHConfig();
        $machine = $request->input('machine', '');
        if(!array_key_exists($machine, $cfg)) {
            return ['code' => 1, 'msg' => trans('admin.machine.not-found')];
        }

        $password = $request->input('password', '');

        $server = $request->input('server', '');
        $server = ServerService::getServer($server);
        if($server === '') {
            return ['code' => 2, 'msg' => trans('admin.server.not-found')];
        }

        // give password
        $server->ssh_pass = $password;

        try {
            $result = MachineService::executeMakerScript($server, ['open_server', $server->node]);

            return ['code' => 0, 'msg' => implode('\n', $result)];
        } catch (\Exception $e) {

            return ['code' => $e->getCode(), 'msg' => $e->getMessage()];
        }
    }
}
