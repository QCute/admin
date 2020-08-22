<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class MergeServerController extends Controller
{
    public function merge(Content $content) 
    {
        if(empty(request()->input("_token", ""))) return $content;
        $src = request()->input("src", "");
        $dst = request()->input("dst", "");
        if($src == $dst) return $content->withError(trans("admin.failed"), trans("admin.merge_same_server"));
        // check server valid
        if(empty($src) || !SwitchServerController::hasServer($src)) return $content->withError(trans("admin.failed"), trans("admin.no_src_server"));
        if(empty($dst) || !SwitchServerController::hasServer($dst)) return $content->withError(trans("admin.failed"), trans("admin.no_dst_server"));
        // conflict resolve sql
        $sql = str_replace("\n", "", preg_replace("/\s*--.*\n?/", "", file_get_contents(env("SERVER_CODE_PATH") . "/script/sql/merge.sql")));
        // other replace
        $sql = str_replace("{{server_id}}", SwitchServerController::getServer($dst)->server_id, $sql);
        // database name replace
        $sql = str_replace("{{dst}}", $dst, str_replace("{{src}}", $src, $sql));
        // import each sql sentence
        foreach(explode(";", $sql) as $row) 
        {
            // use group by field (not in select)
            // set laravel database strict mode as false
            if(!empty(trim($row))) DB::insert($row);
        }
        // query all user and log table
        // $data = DB::select("SELECT `TABLE_NAME` FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = '{$src}' AND `TABLE_NAME` LIKE '%_log' AND `TABLE_NAME` NOT LIKE '%_data'");
        // merge src data to dst
        /// foreach($data as $row) 
        // {
            // DB::insert("INSERT INTO {$dst}.`{$row->TABLE_NAME}` SELECT * FROM {$src}.`{$row->TABLE_NAME}`");
        // }
        // remove src node
        DB::delete("DELETE FROM `server_list_data` WHERE `server_node` = '{$src}'");
        DB::statement("DROP DATABASE IF EXISTS {$src}");
        // remove configure file
        unlink(env("SERVER_CODE_PATH") . "/config/" . $src . ".config");
        // update server list
        SwitchServerController::publicServerList();
        // success tips
        return $content->withSuccess(trans("admin.succeeded"), trans("admin.completed"));
    }

    public function index(Content $content) 
    {
        // merge action
        $content = $this->merge($content);
        // view
        $data = SwitchServerController::getServerList("local");
        $list = implode("", array_map(function($row) { return "<option value='" . $row->server_node . "'>" . $row->server_name . "</option>"; }, $data));
        return $content->body("
            <style>.merge-server-list + .select2-container--default .select2-selection--single {height: 34px !important;}</style>
            <div classs='row'><div class='col-mod-12'><div class='box box-info'>
                <div class='box-header with-border'>合服</div>
                <form name='form' class='form-horizontal' action='merge-server' method='POST' pjax-container>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>从</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control merge-server-list' name='src'>
                                    {$list}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>合并到</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control merge-server-list' name='dst'>
                                    {$list}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>验 证 码</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-check-square-o fa-fw'></i></span>
                                <input type='text' class='form-control' placeholder='验 证 码'>
                                <span class='input-group-btn'><input class='form-control' type='button' value='获取' /></span>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 control-label'></label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-check fa-fw'></i></span>
                                <input type='submit' class='form-control' value='开始' />
                            </div></div>
                        </div>
                    </div>
                </form>
            </div></div></div>
            <link href='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css' rel='stylesheet' />
            <link href='https://cdn.bootcss.com/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css' rel='stylesheet'>
            <script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
            <script>$(document).ready(function() { $('.merge-server-list').select2({placeholder: '选择服务器'}); });</script>
            <script>$(window).resize(function() { $('.merge-server-list').select2({placeholder: '选择服务器'}); });</script>
        ");
    }
}
