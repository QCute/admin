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
            $result = Db::select("SELECT group_concat(`role_name`) AS `role_name`, group_concat(`type`) AS `type`, group_concat(`status`) AS `status` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `role_id` in ('" . implode("','", explode(",", $roleId)) . "')");
            return '{"role_id": "' . $roleId . '",' . '"role_name": "' . $result[0]->role_name . '", "type": "' . $result[0]->type . '", "status": "' . $result[0]->status. '"}';
        } else if (isset($roleName) && !empty($roleName)) {
            $result = Db::select("SELECT group_concat(`role_id`) AS `role_id`, group_concat(`type`) AS `type`, group_concat(`status`) AS `status` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `role_name` in ('" . implode("','", explode(",", $roleName)) . "')");
            return '{"role_id": "' . $result[0]->role_id . '",' . '"role_name": "' . $roleName . '", "type": "' . $result[0]->type . '", "status": "' . $result[0]->status. '"}';
        }
        return "{}";
    }

    public function sendRequest(Content $content)
    {
        // send command
        $server = request()->input("server", "");
        $role_id = request()->input("role_id", "");
        if (empty($server)) return $content;
        if (empty($role_id)) return $content->withError(trans("admin.failed"), trans("admin.no_role"));
        // request
        $array = SwitchServerController::send($server, request()->input("command", ""), json_encode(array("role_id" => $role_id)));
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
            <style> .btn {width: 110px;} </style>
            <script>
                function find() {
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
                <div class='box-header with-border'>" . trans("admin.manage") . "</div>
                <form name='form' class='form-horizontal' action='user-manage' method='POST' pjax-container>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>" . trans("admin.current_server") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control' name='server' style='outline:none;'>
                                    <option value='this'>" . trans("admin.current_server") . "</option>
                                    <option value='all'>" . trans("admin.all_server") . "</option>
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='role_id' class='col-sm-2 asterisk control-label'>" . trans("admin.role_id") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <input type='text' name='role_id' onchange='fill(this.value)' oninput='fill(this.value)' class='form-control' placeholder='" . trans("admin.role_id") . "' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='role_name' class='col-sm-2 control-label'>" . trans("admin.role_name") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <input type='text' name='role_name' onchange='fill(this.value)' oninput='fill(this.value)' class='form-control' placeholder='" . trans("admin.role_name") . "' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='search' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-search fa-fw'></i></span>
                                <input type='button' name='search' class='form-control' onclick='find()' value='" . trans("admin.search") . "' />
                            </div></div>
                        </div>
                        
                        <input type='hidden' name='command' />
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>账号控制: </label>
                            <div class='col-sm-8'><div class='input-group'>
                                <div class='btn-group pull-left'><button type='submit' value='set_role_refuse' class='btn btn-danger' onclick='return set(this.value)'>" . trans("admin.set_role_refuse") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_normal' class='btn btn-success' onclick='return set(this.value)'>" . trans("admin.set_role_normal") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_insider' class='btn btn-info' onclick='return set(this.value)'>" . trans("admin.set_role_insider") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_master' class='btn btn-primary' onclick='return set(this.value)'>" . trans("admin.set_role_master") . "</button></div>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>禁言控制: </label>
                            <div class='col-sm-8'><div class='input-group'>
                                <div class='btn-group pull-left'><button type='submit' value='set_role_chat_unlimited' class='btn btn-success' onclick='return set(this.value)'>" . trans("admin.set_unlimit") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_chat_slient' class='btn btn-danger' onclick='return set(this.value)'>" . trans("admin.set_silent") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_chat_slient_world' class='btn btn-warning' onclick='return set(this.value)'>" . trans("admin.set_silent_world") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_chat_slient_guild' class='btn btn-warning' onclick='return set(this.value)'>" . trans("admin.set_silent_guild") . "</button></div>
                                <div class='btn-group pull-left btn-seq'><button type='submit' value='set_role_chat_slient_private' class='btn btn-warning' onclick='return set(this.value)'>" . trans("admin.set_silent_private") . "</button></div>
                            </div></div>
                        </div>
                    </div>
                </form>
            </div></div></div>
        ");
    }
}
