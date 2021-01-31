<?php

namespace App\Admin\Controllers\GameDataControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class TableViewerController extends Controller
{
    public function index(Content $content)
    {
        $db = SwitchServerController::getCurrentServer();
        $table = request()->input("table", "");
        // default page
        $page = max(request()->input("page", 1) - 1, 0) * 100;
        // key index list
        $keys = DB::select("SELECT `COLUMN_NAME`, `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '{$db}' AND `TABLE_NAME` = '{$table}' AND `COLUMN_KEY` IN ('PRI', 'MUL')");
        // filter        
        $filter = implode("", array_map(function($key){ return " AND " . $key->COLUMN_NAME . " = '" . request()->input($key->COLUMN_NAME) . "'"; }, array_filter($keys, function($key){return null !== (request()->input($key->COLUMN_NAME)); })));
        // search index
        $search = implode("", array_map(function($key){ return " <div class='form-group'><input type='text' class='form-control filter-field' name='{$key->COLUMN_NAME}' value='" . request()->input($key->COLUMN_NAME) . "' placeholder='{$key->COLUMN_COMMENT}'></div>"; }, $keys));
        // head column
        $comment = DB::SELECT("SELECT `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '{$db}' AND `TABLE_NAME` = '{$table}'");
        $table_text = "<tr>" . implode("", array_map(function($row){ return "<th class='table-title' title='{$row->COLUMN_COMMENT}'>" . mb_substr($row->COLUMN_COMMENT, 0, 8) . "</th>"; }, $comment)) . "</tr>";
        // row data list
        $data = DB::select("SELECT * FROM {$db}.{$table} WHERE 1 = 1 {$filter} LIMIT {$page}, 100");
        $table_text .= implode("", array_map(function($row){ return "<tr class='table-row'>" . implode("", array_map(function($value){ return "<td class='table-data'>" . $value . "</td>"; }, json_decode(json_encode($row) ,true))) . "</tr>"; }, $data));
        // body
        return $content->title('')->body("
            <style>
                .table-title{min-width:8em;}
                .table-row{max-height:0.2em;}
                .btn-x{border-radius: unset;}
                .input-group-btn-ok-x{width:4em;}
                .input-group-content-x{max-width:8em;}
                .input-group-addon-x{border: none;}
                .panel-x{border-radius: 0px;}
            </style>
            <form action='" . request()->path() . "?table={$table}' class='form-inline' id='filter'>
                {$search}
                <a class='btn btn-default' style='background-color:white;' onclick=\"this.href = 'table-data-viewer?table={$table}&' + Array.prototype.slice.call(document.getElementsByClassName('filter-field')).filter((e)=>e.value.trim().length!==0).map((e)=>e.name + '=' + e.value).join('&')\">" . trans("admin.ok") . "</a>
            </form>
            <br/>
            <div class='panel panel-default' style='overflow:auto;border-radius: 0px;'>
                <table class='table table-bordered table-hover' style='min-width:640px;' data-toggle='table'>
                    {$table_text}
                </table>
            </div>
        ");
    }
}
