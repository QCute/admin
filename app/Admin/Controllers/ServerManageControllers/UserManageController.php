<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class UserManageController extends Controller
{
    public function search(Content $content)
    {
        $roleId = request()->input('role_id');
        $roleName = request()->input('role_name');
        if (isset($roleId) && !empty($roleId)) {
            $result = Db::select("SELECT group_concat(`role_name`) as `role_name` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `role_id` in ('" . implode("','", explode(",", $roleId)) . "')");
            return '{"role_id": "' . $roleId . '",' . '"role_name": "' . $result[0]->role_name . '"}';
        } else if (isset($roleName) && !empty($roleName)) {
            $result = Db::select("SELECT group_concat(`role_id`) as `role_id` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `role_name` in ('" . implode("','", explode(",", $roleName)) . "')");
            return '{"role_id": "' . $result[0]->role_id . '",' . '"role_name": "' . $roleName . '"}';
        }
        return "{}";
    }

    public function sendRequest(Content $content)
    {
        // send command
        $server = request()->input("server", "");
        $role = request()->input("role_id", "");
        if (empty($server)) return $content;
        if (empty($role)) return $content->withError(trans("admin.failed"), trans("admin.no_role"));
        // request
        $array = SwitchServerController::send($server, request()->input("command", ""), json_encode(array("role_id" => $role)));
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

    public function index(Content $content)
    {
        $content = $this->sendRequest($content);
        // view
        return $content->body("
            <style> .btn-seq {margin-left: 5px;} </style>
            <script>
                function search() {
                    $.ajax({
                        type: 'GET',
                        url: 'user-manage-search',
                        data: {'role_id' : $(\"[name='role_id']\")[0].value, 'role_name': $(\"[name='role_name']\")[0].value},
                        success: (data) => { JSON.parse(data, (name, value) => {if(typeof value == 'string') $('[name=' + name + ']')[0].value = value;}) },
                        error: (jqXHR, textStatus, errorThrown) => alert(errorThrown)
                    });
                }
                function fill(value) {
                    Array.from($('.manage')).forEach((e) => e.href='user-manage?action=' + e.name + '&role_id=' + value);
                }
                function clear() {
                    Array.from($('.manage')).forEach((e) => e.href='' );
                }
                function set(value) {
                    return ($(\"[name='command']\")[0].value = value) !== undefined;
                }
            </script>
            
            <div classs='row'><div class='col-mod-12'><div class='box box-info'>
                <div class='box-header with-border'>管理</div>
                <form name='form' class='form-horizontal' action='user-manage' method='POST' pjax-container>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>服务器</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control' name='server' style='outline:none;'>
                                    <option value='this'>当前服</option>
                                    <option value='all'>全服</option>
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>玩家ID</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <input type='text' name='role_id' onchange='fill(this.value)' oninput='fill(this.value)' class='form-control' placeholder='玩家ID' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'>玩家名</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <input type='text' name='role_name' onchange='fill(this.value)' oninput='fill(this.value)' class='form-control' placeholder='玩家名' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-search fa-fw'></i></span>
                                <input type='button' class='form-control' onclick='search()' value='查找' />
                            </div></div>
                        </div>
                        
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'><div class='input-group'>
                                <input type='hidden' name='command' />
                                <div class='btn-group pull-left'><button type='submit' value='ban_chat' class='btn btn-primary' onclick='return set(this.value)'>" . trans("admin.ban_chat") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='allow_chat' class='btn btn-primary' onclick='return set(this.value)'>" . trans("admin.allow_chat") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_refuse' class='btn btn-primary' onclick='return set(this.value)'>" . trans("admin.set_role_refuse") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_normal' class='btn btn-primary' onclick='return set(this.value)'>" . trans("admin.set_role_normal") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_insider' class='btn btn-primary' onclick='return set(this.value)'>" . trans("admin.set_role_insider") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_master' class='btn btn-primary' onclick='return set(this.value)'>" . trans("admin.set_role_master") . "</button></div>
                            </div></div>
                        </div>
                    </div>
                </form>
            </div></div></div>
        ");
    }
}
