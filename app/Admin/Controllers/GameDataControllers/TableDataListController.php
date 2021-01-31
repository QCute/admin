<?php

namespace App\Admin\Controllers\GameDataControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class TableDataListController extends Controller
{

    public function user(Content $content)
    {
        $database = SwitchServerController::getCurrentServer();
        $data = DB::SELECT("SELECT `TABLE_COMMENT`, `TABLE_NAME` FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` NOT LIKE '%_log' AND `TABLE_NAME` NOT LIKE '%_data'", [$database]);
        $html = implode("", array_map(function($row){ return "<tr><td>" . $row->TABLE_COMMENT . "</td><td>" . $row->TABLE_NAME . "</td><td><a href='table-data-viewer?table={$row->TABLE_NAME}'>" . trans("admin.lookup") . "</a></td></tr>"; }, $data));
        return $content->title('')->body("
            <style>.panel{border-radius: 0px;}</style>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.name") . "</th><th>" . trans("admin.table") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }

    public function configure(Content $content)
    {
        $database = SwitchServerController::getCurrentServer();
        $data = DB::SELECT("SELECT `TABLE_COMMENT`, `TABLE_NAME` FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` LIKE '%_data'", [$database]);
        $html = implode("", array_map(function($row){ return "<tr><td>" . $row->TABLE_COMMENT . "</td><td>" . $row->TABLE_NAME . "</td><td><a href='table-data-viewer?table={$row->TABLE_NAME}'>" . trans("admin.lookup") . "</a></td></tr>"; }, $data));
        return $content->title('')->body("
            <style>.panel{border-radius: 0px;}</style>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.name") . "</th><th>" . trans("admin.table") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }

    public function log(Content $content)
    {
        $database = SwitchServerController::getCurrentServer();
        $data = DB::SELECT("SELECT `TABLE_COMMENT`, `TABLE_NAME` FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` LIKE '%_log'", [$database]);
        $html = implode("", array_map(function($row){ return "<tr><td>" . $row->TABLE_COMMENT . "</td><td>" . $row->TABLE_NAME . "</td><td><a href='table-data-viewer?table={$row->TABLE_NAME}'>" . trans("admin.lookup") . "</a></td></tr>"; }, $data));
        return $content->title('')->body("
            <style>.panel{border-radius: 0px;}</style>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.name") . "</th><th>" . trans("admin.table") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }

}
