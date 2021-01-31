<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class MergeServerController extends Controller
{
    private static function replace(Array $array, String $input, String $output = "")
    {
        $output = empty($output) ? $input : $output;
        // read file
        $input = file_get_contents($input);
        // replace loop
        foreach($array as $one)
        {
            // match row and field by regex
            preg_match($one["row"], $input, $row);
            preg_match($one["field"], $row[0], $field);
            // calculate space padding
            $space = strlen($row[0]) - strlen(rtrim($row[0]));
            $diff = strlen($one["replace"]) - strlen($field[0]);
            // generate padding
            // $padding = implode("", array_map(function($_){ return " "; }, range(0, $space - $diff - 1)));
            $padding = implode("", array_pad([], $space - $diff, " "));
            // replace into field and row
            $row = preg_replace($one["field"], $one["replace"], rtrim($row[0]));
            $input = preg_replace($one["row"], $row . $padding, $input);
        }
        // write file
        file_put_contents($output, $input);
    }

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
        // server id
        $src_server_id = SwitchServerController::getServer($src)->server_id;
        $dst_server_id = SwitchServerController::getServer($dst)->server_id;
        // conflict resolve sql
        $sql = str_replace("\n", "", preg_replace("/\s*--.*\n?/", "", file_get_contents(env("SERVER_PATH") . "/script/sql/merge.sql")));
        // other replace
        $sql = str_replace("{{src_server_id}}", $src_server_id, $sql);
        $sql = str_replace("{{dst_server_id}}", $dst_server_id, $sql);
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
            DB::delete("DELETE FROM `server_list` WHERE `server_node` = ?", [$src]);
        } else {
            // update entrance
            DB::update("UPDATE `server_list` SET `server_id` = (SELECT `server_id` FROM `server_list` WHERE server_node = ?), `server_port` = (SELECT `server_port` FROM `server_list` WHERE server_node = ?) where server_node = ?", [$dst, $dst, $src]);
        }
        // drop database
        DB::statement("DROP DATABASE IF EXISTS {$src}");
        // take and merge the server id list
        $src_config = env("SERVER_PATH") . "/config/{$src}.config";
        $dst_config = env("SERVER_PATH") . "/config/{$dst}.config";
        $src_server_id_list = shell_exec("erl -noinput -boot start_clean -eval \"erlang:display(proplists:get_value(server_id_list, proplists:get_value(main, hd(element(2, file:consult(\\\"{$src_config}\\\"))), []), [])),erlang:halt().\" 2>&1");
        $dst_server_id_list = shell_exec("erl -noinput -boot start_clean -eval \"erlang:display(proplists:get_value(server_id_list, proplists:get_value(main, hd(element(2, file:consult(\\\"{$dst_config}\\\"))), []), [])),erlang:halt().\" 2>&1");
        $now = strtotime(date("Y-m-d", time()));
        $server_id_list = shell_exec("erl -noinput -boot start_clean -eval \"erlang:display([{{$src_server_id}, {$now}}] ++ {$src_server_id_list} ++ {$dst_server_id_list}),erlang:halt().\" 2>&1");
        // write dst configure file merge server id list
        $list = [
            ["row" => "/\{\s*server_id_list\s*,\s*\[.*?\]\s*\},?\s*/", "field" => "/\[.*?\]/", "replace" => $server_id_list],
        ];
        self::replace($list, env("SERVER_PATH") . "/config/{$dst}.config");
        // remove src configure file
        unlink(env("SERVER_PATH") . "/config/{$src}.config");
        // republic server list
        SwitchServerController::publishServerList();
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
        return $content->title('')->body("
            <div class='box box-info'>
                <div class='box-header with-border'>" . trans("admin.merge_server") . "</div>
                <form name='form' class='form-horizontal' action='" . request()->path() . "' method='POST' pjax-container>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='src' class='col-sm-2 asterisk control-label'>" . trans("admin.merge_from") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control merge-server-list' name='src'>
                                    {$list}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='dst' class='col-sm-2 asterisk control-label'>" . trans("admin.merge_to") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control merge-server-list' name='dst'>
                                    {$list}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='merge' class='col-sm-2 asterisk control-label'>" . trans("admin.merge_mode") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <input type='radio' id='merge-keep' name='merge' value='keep' class='iradio_minimal-blue' checked/> 
                                <span for='merge-keep' class='input-group-addon' style='border:unset;width:unset;'>" . trans("admin.merge_mode_keep") . "</span>
                                <input type='radio' id='merge-it' name='merge' value='merge' class='iradio_minimal-blue'/> 
                                <span for='merge-it' class='input-group-addon' style='border:unset;width:unset;'>" . trans("admin.merge_mode_merge") . "</span>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='check' class='col-sm-2 asterisk control-label'>验 证 码</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-check-square-o fa-fw'></i></span>
                                <input type='text' name='check' class='form-control' placeholder='验 证 码'>
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
            </div>

            <script>$(document).ready(function() { $('.merge-server-list').select2({placeholder: '" . trans("admin.server") . "'}); });</script>
            <script>$(window).resize(function() { $('.merge-server-list').select2({placeholder: '" . trans("admin.server") . "'}); });</script>
            <script>$(document).ready(function(){ $('input').iCheck({ checkboxClass: 'icheckbox_square-blue', radioClass: 'iradio_square-blue' }); });</script>
        ");
    }
}
