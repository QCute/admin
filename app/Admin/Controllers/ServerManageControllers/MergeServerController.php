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
        $mode = request()->input("mode", "");
        if($src == $dst) return $content->withError(trans("admin.failed"), trans("admin.merge_same_server"));
        // check server valid
        if(empty($src) || !SwitchServerController::hasServer($src)) return $content->withError(trans("admin.failed"), trans("admin.no_src_server"));
        if(empty($dst) || !SwitchServerController::hasServer($dst)) return $content->withError(trans("admin.failed"), trans("admin.no_dst_server"));
        // conflict resolve sql
        $sql = str_replace("\n", "", preg_replace("/\s*--.*\n?/", "", file_get_contents(env("SERVER_CODE_PATH") . "/script/sql/merge.sql")));
        // other replace
        $sql = str_replace("{{src_server_id}}", SwitchServerController::getServer($src)->server_id, $sql);
        $sql = str_replace("{{dst_server_id}}", SwitchServerController::getServer($dst)->server_id, $sql);
        // database name replace
        $sql = str_replace("{{dst}}", $dst, str_replace("{{src}}", $src, $sql));
        // import each sql sentence
        foreach(explode(";", $sql) as $row) 
        {
            // use group by field (not in select)
            // set laravel database strict mode as false
            if(!empty(trim($row))) DB::insert($row);
        }
        // default
        if ($mode == "") {
            // delete entrance
            DB::delete("DELETE FROM `server_list_data` WHERE `server_node` = '{$src}'");
        } else {
            // update entrance
            DB::update("UPDATE `server_list_data` SET `server_id` = (SELECT `server_id` FROM `server_list_data` WHERE server_node = '{$dst}'), `server_port` = (SELECT `server_port` FROM `server_list_data` WHERE server_node = '{$dst}') where server_node = '{$src}'");
        }
        // drop database
        DB::statement("DROP DATABASE IF EXISTS {$src}");
        // remove configure file
        unlink(env("SERVER_CODE_PATH") . "/config/" . $src . ".config");
        // republic server list
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
                <div class='box-header with-border'>" . trans("admin.merge_server") . "</div>
                <form name='form' class='form-horizontal' action='merge-server' method='POST' pjax-container>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>" . trans("admin.merge_from") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control merge-server-list' name='src'>
                                    {$list}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>" . trans("admin.merge_to") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control merge-server-list' name='dst'>
                                    {$list}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='world' class='col-sm-2 asterisk control-label'>" . trans("admin.merge_mode") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <input type='radio' name='merge' value='' class='iradio_minimal-blue' checked/> " . trans("admin.merge_mode_merge") . " 
                                <span style='margin-left: 2em;'>&nbsp;</span>
                                <input type='radio' name='merge' value='keep' class='iradio_minimal-blue' /> " . trans("admin.merge_mode_keep") . "
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
                                <input type='submit' class='form-control' value='" . trans("admin.start") . "' />
                            </div></div>
                        </div>
                    </div>
                </form>
            </div></div></div>
            <link href='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css' rel='stylesheet' />
            <link href='https://cdn.bootcss.com/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css' rel='stylesheet'>
            <script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
            <script>$(document).ready(function() { $('.merge-server-list').select2({placeholder: '" . trans("admin.server") . "'}); });</script>
            <script>$(window).resize(function() { $('.merge-server-list').select2({placeholder: '" . trans("admin.server") . "'}); });</script>
            <script>$(document).ready(function(){ $('input').iCheck({ checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%' }); });</script>
        ");
    }
}
