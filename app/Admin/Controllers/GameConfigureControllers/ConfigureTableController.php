<?php

namespace App\Admin\Controllers\GameConfigureControllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class ConfigureTableController extends Controller
{
    public function index(Content $content)
    {
        // action
        $action = request()->input("action", "");
        switch ($action) 
        {
            case "export": 
            {
                // ensure dir
                if (!file_exists(storage_path("admin") . "/xml/")) mkdir (storage_path("admin") . "/xml",0755,true);
                // generate xml file
                exec("cd " . storage_path("admin") . "/xml/" . " && " . env("SERVER_CODE_PATH") . "/script/shell/maker.sh xml " . request()->input("table") . " 2>&1", $result);
                // download xml file
                $file = storage_path("admin") . "/xml/" . request()->input("xml") . ".xml";
                if (file_exists($file))
                    // pjax use location.href redirection to download file or use ajax 
                    return response()->download($file, basename($file), ["Content-Type: text/xml"]);
                else
                    // retuan file not found
                    return response(json_encode($result), 404);
            }
            case "import": 
            {
                // file upload
                $file = request()->file("xml");
                $boolResult = $file->storeAs(env("XML_PATH", "xml"), $file->getClientOriginalName(), array("disk" => "admin"));
                if(!$boolResult) return "toastr.error(" . json_encode(trans("admin.upload-error")) . ")";
                // ensure dir
                if (!file_exists(storage_path("admin") . "/xml/")) mkdir (storage_path("admin") . "/xml",0755,true);
                // import table data
                exec("cd " . storage_path("admin") . "/xml/" . " && " . env("SERVER_CODE_PATH") . "/script/shell/maker.sh table " . $file->getClientOriginalName() . " 2>&1", $result);
                // delete file
                unlink(storage_path("admin") . "/xml/" . $file->getClientOriginalName());
                // handle result
                $result = implode("", $result);
                if ($result == "ok") {
                    DB::insert("INSERT INTO `table_import_log` (`username`, `name`, `table_name`) VALUES ('" . Auth::user()->name . "', '" . basename($file->getClientOriginalName(), ".xml") . "', '')");
                    $result = "toastr.success(" . json_encode(trans("admin.succeeded")) . ")";
                }
                else
                    $result = "toastr.error(" . json_encode($result) . ")";
            }break;
            default:
            {
                if (empty($action)) 
                    $result = "";
                else 
                    $result = "toastr.error(" . json_encode("unknown action: " . request()->input("action")) . ")";
            }
        }
        // view
        $data = DB::select("SELECT `TABLES`.`TABLE_COMMENT`, `TABLES`.`TABLE_NAME`, `table_import_log`.* FROM information_schema.`TABLES` LEFT JOIN (SELECT `table_import_log`.`username`, `table_import_log`.`name`, `table_import_log`.`time` FROM `table_import_log` JOIN (SELECT MAX(`id`) AS `id` FROM `table_import_log` GROUP BY `name`) AS `group_table_import_log` ON `table_import_log`.`id` = `group_table_import_log`.`id`) AS `table_import_log` ON `TABLES`.`TABLE_COMMENT` = `table_import_log`.`name` WHERE `TABLE_SCHEMA` = '" . SwitchServerController::getCurrentServer() . "' AND `TABLE_NAME` LIKE '%_data'");
        $html = implode("", array_map(function($row){ return "<tr><td>{$row->TABLE_COMMENT}</td><td>{$row->TABLE_NAME}</td><td>{$row->username}</td><td>{$row->time}</td><td><a class='action' onclick='$.fileDownload(\"configure-table?action=export&table={$row->TABLE_NAME}&xml={$row->TABLE_COMMENT}\").fail(result => toastr.error(result))'>" . trans("admin.export") . "</tr>"; }, $data));
        return $content->body("
            <script>$(document).ready(function(){{$result}});</script>
            <style>
                .action{cursor: pointer;}
                .panel{border-radius: 0px;}
            </style>
            <div class=input-group file-caption-main'>
                <div class='file-caption form-control  kv-fileinput-caption icon-visible'>
                    <span class='file-caption-icon'><i class='glyphicon glyphicon-file'></i></span>
                    <input class='file-caption-name' onkeydown='return false;' onpaste='return false;' id='filename' placeholder='选择文件'>
                </div>
                <div class='input-group-btn input-group-append'>
                    <form action='configure-table' method='POST' enctype='multipart/form-data' pjax-container>
                        " . csrf_field() . "
                        <input type='hidden' name='action' value='import'>
                        <div class='btn btn-primary btn-file'><i class='glyphicon glyphicon-folder-open'></i>&nbsp;  <span class='hidden-xs'>" . trans("admin.browse") . "</span><input type='file' onchange='document.getElementById(\"filename\").value = this.value.split(/\\\|\//g).pop()' name='xml'></div>
                        <input class='btn btn-primary btn-file' type='submit' value='" . trans("admin.import") . "'/>
                    </form>
                </div>
            </div>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.name") . "</th><th>" . trans("admin.table") . "</th><th>" . trans("admin.username") . "</th><th>" . trans("admin.time") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
            <script src='https://cdn.bootcss.com/jquery.fileDownload/1.4.2/jquery.fileDownload.min.js'></script>
        ");
    }
}
