<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class ServerNoticeController extends Controller
{
    public function send(Content $content)
    {
        // send notice
        $server = request()->input("server", "");
        if (empty($server)) return $content;
        // construct data
        $json = json_encode(array("title" => request()->input("title"), "content" => request()->input("content"), "item" => request()->input("item")));
        // request
        $array = SwitchServerController::send($server, "notice", $json);
        // handle result
        $ok = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["ok"]), $array["ok"]));
        $error = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["error"]), $array["error"]));
        // toast tips
        if (!empty($ok))
            return $content->withSuccess(trans("admin.succeeded"), $ok);
        if (!empty($error))
            return $content->withError(trans("admin.failed"), $error);
        return $content;
    }

    public function index(Content $content)
    {
        $content = $this->send($content);
        // view
        return $content->body("
            <div classs='row'><div class='col-mod-12'><div class='box box-info'>
                <div class='box-header with-border'>编辑公告</div>
                <form name='form' class='form-horizontal' action='server-notice' method='POST' pjax-container>
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
                            <label for='name' class='col-sm-2 asterisk control-label'>标题</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <input type='text' class='form-control' name='title' placeholder='标题' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>内容</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <input type='text' class='form-control' name='title' placeholder='内容' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'>物品</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <input type='text' class='form-control' name='title' placeholder='物品' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-send-o fa-fw'></i></span>
                                <input type='submit' class='form-control' value='发送' />
                            </div></div>
                        </div>
                    </div>
                </form>
            </div></div></div>
        ");
    }
}