<?php

namespace App\Admin\Controllers\OperationControllers;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class GameMailController extends Controller
{
    public function send(Content $content)
    {
        $server = request()->input("server", "");
        if (empty($server)) return $content;
        // construct data
        $args = [
            "title" => request()->input("title", ""),
            "content" => request()->input("content", ""),
            "items" => request()->input("items", ""),
            "role_id" => request()->input("role_id", ""),
        ];
        // request
        $array = SwitchServerController::send($server, "mail", json_encode($args));
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
        return $content->title('')->body("
            <div class='box box-info'>
                <div class='box-header with-border'>" . trans("admin.mail") . "</div>
                <form name='form' class='form-horizontal' action='" . request()->path() . "' method='POST' pjax-container>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='server' class='col-sm-2 asterisk control-label'>" . trans("admin.server") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control' name='server' style='outline:none;'>
                                    <option value='this'>" . trans("admin.current_server") . "</option>
                                    <option value='all'>" . trans("admin.all_server") . "</option>
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='title' class='col-sm-2 asterisk control-label'>" . trans("admin.title") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <textarea class='form-control' rows='10' name='title'></textarea>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='content' class='col-sm-2 asterisk control-label'>" . trans("admin.content") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <textarea class='form-control' rows='10' name='content'></textarea>
                                
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='items' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='help-block'><i class='fa fa-info-circle'></i> 物品可使用<a href='/configure-assistant' target='_blank'>配表助手</a>生成</span>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='items' class='col-sm-2 control-label'>" . trans("admin.items") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <textarea class='form-control' rows='10' name='items'></textarea>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='role_id' class='col-sm-2 asterisk control-label'>" . trans("admin.role_id") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-pencil fa-fw'></i></span>
                                <textarea class='form-control' rows='10' name='items'></textarea>
                                
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-send-o fa-fw'></i></span>
                                <input type='submit' class='form-control' value='" . trans("admin.send") . "' />
                            </div></div>
                        </div>
                    </div>
                </form>
            </div>
        ");
    }
}
