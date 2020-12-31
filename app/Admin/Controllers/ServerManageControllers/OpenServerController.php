<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class OpenServerController extends Controller
{
    private static function replace(Array $array, String $input)
    {
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
            $padding = implode("", array_pad(array(), $space - $diff, " "));
            // replace into field and row
            $row = preg_replace($one["field"], $one["replace"], rtrim($row[0]));
            $input = preg_replace($one["row"], $row . $padding, $input);
        }
        // return replace result
        return $input;
    }
    public function open(Content $content) 
    {
        if(empty(request()->input("_token", ""))) return $content;
        $name = request()->input("name", "");
        $tab = request()->input("tab", "");
        $center = request()->input("center", "");
        $world = request()->input("world", "");
        $recommend = request()->input("recommend", "");
        // $isConnectWorld = request()->input('is_connect_world');
        if(empty($name)) return $content->withError(trans("admin.failed"), trans("admin.empty_name"));
        if(!empty($dst) && ($center != "undefined" && !SwitchServerController::hasServer($center))) return $content->withError(trans("admin.failed"), trans("admin.invalid_center"));
        // node 
        $server_id = SwitchServerController::nextServerId("local");
        $port = SwitchServerController::nextServerPort("local");
        $node = basename(env("SERVER_CODE_PATH", "erlang")) . "_" . $server_id;
        // create new database
        DB::statement("CREATE DATABASE IF NOT EXISTS {$node} DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci");
        // import sql
        $sql = str_replace("\n", "", preg_replace("/\s*--.*\n?/", "", file_get_contents(env("SERVER_CODE_PATH") . "/script/sql/open.sql")));
        // new database connection
        $connection = new Connection(new \PDO("mysql:host=" . env("DB_HOST") . ";port=" . env("DB_PORT") . ";dbname=" . $node, env("DB_USERNAME"), env("DB_PASSWORD")));
        // import each sql sentence
        foreach(explode(";", $sql) as $row) 
        {
            // use group by field (not in select)
            // set laravel database strict mode as false
            if(!empty(trim($row))) $connection->insert($row);
        }
        // insert node data
        DB::insert("INSERT INTO `server_list_data` (`server_node`, `server_name`, `server_port`, `server_id`, `server_type`, `open_time`, `tab_name`, `state`, `recommend`) VALUES ('{$node}', '{$name}', {$port}, {$server_id}, 'local', " . time(). ", '{$tab}',  1, '${recommend}')");
        //  generate erlang config file
        $config = env("SERVER_CODE_PATH") . "/config/example/local.config.example";
        // read
        $config = file_get_contents($config);
        $list = array(
            array("row" => "/\{\s*database\s*,\s*.*?\},?\s*/", "field" => "/(?<=\").*?(?=\")/", "replace" => $node),
            array("row" => "/\{\s*server_id\s*,\s*.*?\},?\s*/", "field" => "/\d+/", "replace" => $server_id),
            array("row" => "/\{\s*open_time\s*,\s*.*?\},?\s*/", "field" => "/\d+/", "replace" => strtotime(date("Y-m-d", time()))),
            array("row" => "/\{\s*center_node\s*,\s*.*?\},?\s*/", "field" => "/\w+(?=\s*\})/", "replace" => "\"{$center}\""),
            array("row" => "/\{\s*center_ip\s*,\s*.*?\},?\s*/", "field" => "/(?<=\").*?(?=\")/", "replace" => ""),
            array("row" => "/\{\s*world_node\s*,\s*.*?\},?\s*/", "field" => "/\w+(?=\s*\})/", "replace" => "\"{$world}\""),
            array("row" => "/\{\s*world_ip\s*,\s*.*?\},?\s*/", "field" => "/(?<=\").*?(?=\")/", "replace" => ""),
        );
        $config = OpenServerController::replace($list, $config);
        // write
        file_put_contents(env("SERVER_CODE_PATH") . "/config/" . $node . ".config", $config);
        // update server list
        SwitchServerController::publicServerList();
        // success tips
        return $content->withSuccess(trans("admin.succeeded" . $center), trans("admin.completed"));
    }

    public function index(Content $content) 
    {
        // open
        $content = $this->open($content);
        // view
        // center option
        $centerList = SwitchServerController::getServerList("center");
        $centerList = implode("", array_map(function($row) { return "<option value='" . $row->server_node . "'>" . $row->server_name . "</option>"; }, $centerList));
        // recommend option
        $serverRecommendList = trans("admin.server_recommend");
        $serverRecommendList = implode("", array_map(function($row) { return "<option value='" . $row . "'>" . $row . "</option>"; }, array_values($serverRecommendList)));
        return $content->body("
            <style>.open-server-list + .select2-container--default .select2-selection--single {height: 34px !important;}</style>
            <div classs='row'><div class='col-mod-12'><div class='box box-info'>
                <div class='box-header with-border'>" . trans("admin.open_server") . "</div>
                <form name='form' class='form-horizontal' action='open-server' method='POST' pjax-container>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='name' class='col-sm-2 asterisk control-label'>" . trans("admin.name") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <input type='text' class='form-control' name='name' placeholder='" . trans("admin.name") . "' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='tab' class='col-sm-2 asterisk control-label'>" . trans("admin.tab") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <input type='text' class='form-control' name='tab' placeholder='" . trans("admin.tab") . "' aria-describedby='basic-addon3'>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='recommend' class='col-sm-2 asterisk control-label'>" . trans("admin.state") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control open-server-list' name='recommend' style='outline:none;'>
                                    {$serverRecommendList}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='center' class='col-sm-2 asterisk control-label'>" . trans("admin.center_name") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-list fa-fw'></i></span>
                                <select class='form-control open-server-list' name='center'>
                                    <option value='undefined'>" . trans("admin.no_center") . "</option>
                                    {$centerList}
                                </select>
                            </div></div>
                        </div>
                        <div class='form-group'>
                            <label for='world' class='col-sm-2 asterisk control-label'>" . trans("admin.connect_world") . "</label>
                            <div class='col-sm-8'><div class='input-group'>
                                <input type='radio' name='world' value='world' class='iradio_minimal-blue' checked/> " . trans("admin.yes") . " 
                                <span style='margin-left: 2em;'>&nbsp;</span>
                                <input type='radio' name='world' value='' class='iradio_minimal-blue' /> " . trans("admin.no") . "
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
                                <input type='submit' class='form-control' value='" . trans("admin.ok") . "' />
                            </div></div>
                        </div>
                    </div>
                </form>
            </div></div></div>
            <link href='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css' rel='stylesheet' />
            <link href='https://cdn.bootcss.com/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css' rel='stylesheet'>
            <script src='https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'></script>
            <script>$(document).ready(function() { $('.open-server-list').select2({placeholder: '" . trans("admin.server") . "'}); });</script>
            <script>$(window).resize(function() { $('.open-server-list').select2({placeholder: '" . trans("admin.server") . "'}); });</script>
            <script>$(document).ready(function(){ $('input').iCheck({ checkboxClass: 'icheckbox_minimal-blue', radioClass: 'iradio_minimal-blue', increaseArea: '20%' }); });</script>
        ");
    }
}
