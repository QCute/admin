<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class ServerListManageController extends Controller
{
    public function index(Content $content)
    {
        $content = $this->sendRequest($content);
        $data = SwitchServerController::getServerList("local");
        $html = implode("", array_map(function($row){ return "<tr><td>" . $row->server_name . "</td><td>" . $row->center_name . "</td><td>" . $row->world . "</td><td>" . date("Y-m-d H:i:s", $row->open_time) . "</td><td>" . $row->state . "</td><td><a href='server-list-manage?command=set_state_refuse&server={$row->server_node}'>" . trans("admin.refuse") . "</a> | <a href='server-list-manage?command=set_state_normal&server={$row->server_node}'>" . trans("admin.normal") . "</a> | <a href='server-list-manage?command=set_state_insider&server={$row->server_node}'>" . trans("admin.insider") . "</a> | <a href='server-list-manage?command=set_state_master&server={$row->server_node}'>" . trans("admin.master") . "</a></td></tr>"; }, $data));
        return $content->body("
            <style>.panel{border-radius: 0px;}</style>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.name") . "</th><th>" . trans("admin.center_name") . "</th><th>" . trans("admin.connect_world") . "</th><th>" . trans("admin.open_time") . "</th><th>" . trans("admin.state") . "</th><th>" . trans("admin.set_state") . " (<a href='server-list-manage-public'>" . trans("admin.public_server_list") . "</a>)</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }
    
    public function public(Content $content)
    {
        SwitchServerController::publicServerList();
        return $this->index($content->withSuccess(trans("admin.succeeded")));
    }

    public function sendRequest(Content $content)
    {
        // send command
        $server = request()->input("server", "");
        if (empty($server)) return $content;
        // request
        $array = SwitchServerController::send($server, request()->input("command", ""), json_encode(array()));
        // handle result
        $ok = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["ok"]), $array["ok"]));
        $error = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["error"]), $array["error"]));
        // toast tips
        if (!empty($ok))
            return $content->withSuccess(trans("admin.succeeded"), $ok);
        else if (!empty($error))
            return $content->withError(trans("admin.failed"), $error);
        else
            return $content;
    }
}
