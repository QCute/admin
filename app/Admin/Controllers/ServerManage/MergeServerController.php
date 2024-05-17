<?php

namespace App\Admin\Controllers\ServerManage;

use App\Admin\Builders\Form;
use App\Admin\Controllers\Controller;
use App\Admin\Services\Extend\MachineService;
use App\Admin\Services\Extend\ServerService;
use Illuminate\Http\Request;

class MergeServerController extends Controller
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->name(trans('admin.server.merge'));

        $servers = ServerService::getServers();

        $select = $form->select('from')->label(trans('admin.server.from'));
        foreach($servers as $server) {
            if($server->type === '') continue;
            if($server->type == 'center') continue;
            if($server->type == 'world') continue;
            $select->option()->label($server->name)->value($server->id);
        }

        $select = $form->select('to')->label(trans('admin.server.to'));
        foreach($servers as $server) {
            if($server->type === '') continue;
            if($server->type == 'center') continue;
            if($server->type == 'world') continue;
            $select->option()->label($server->name)->value($server->id);
        }

        return $form->build();
    }

    public function submit(Request $request)
    {
        $from = $request->input('from', '');
        $from = ServerService::getServer($from);
        if($from === '') {
            return back()->withErrors(trans(''));
        }

        $to = $request->input('to', '');
        $to = ServerService::getServer($to);
        if($to === '') {
            return back()->withErrors(trans(''));
        }

        if($from->id == $to->id) {
            return back()->withErrors(trans(''));
        }

        MachineService::executeMakerScript($from, ['merge_server', $from, $to]);

        // change data
        $to->where('id', '=', $to->id)->update(['server_id' => $from->server_id, 'server_port' => $from->server_port]);

        // success
        return ['code' => 0, 'msg' => ''];
    }
}
