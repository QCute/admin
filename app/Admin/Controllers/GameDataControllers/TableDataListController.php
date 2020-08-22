<?php

namespace App\Admin\Controllers\GameDataControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class TableDataListController extends Controller
{

    public function showRole(Content $content)
    {
        $data = DB::SELECT("SELECT `TABLE_COMMENT`, `TABLE_NAME` FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = '" . SwitchServerController::getCurrentServer() . "' AND `TABLE_NAME` NOT LIKE '%_log' AND `TABLE_NAME` NOT LIKE '%_data'");
        $html = implode("", array_map(function($row){ return "<tr><td>" . $row->TABLE_COMMENT . "</td><td>" . $row->TABLE_NAME . "</td><td><a href='table-viewer?table={$row->TABLE_NAME}'>" . trans("admin.lookup") . "</a></td></tr>"; }, $data));
        return $content->body("
            <style>.panel{border-radius: 0px;}</style>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.name") . "</th><th>" . trans("admin.table") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }

    public function showConfigure(Content $content)
    {
        $data = DB::SELECT("SELECT `TABLE_COMMENT`, `TABLE_NAME` FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = '" . SwitchServerController::getCurrentServer() . "' AND `TABLE_NAME` LIKE '%_data'");
        $html = implode("", array_map(function($row){ return "<tr><td>" . $row->TABLE_COMMENT . "</td><td>" . $row->TABLE_NAME . "</td><td><a href='table-viewer?table={$row->TABLE_NAME}'>" . trans("admin.lookup") . "</a></td></tr>"; }, $data));
        return $content->body("
            <style>.panel{border-radius: 0px;}</style>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.name") . "</th><th>" . trans("admin.table") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }

    public function showLog(Content $content)
    {
        $data = DB::SELECT("SELECT `TABLE_COMMENT`, `TABLE_NAME` FROM information_schema.`TABLES` WHERE `TABLE_SCHEMA` = '" . SwitchServerController::getCurrentServer() . "' AND `TABLE_NAME` LIKE '%_log'");
        $html = implode("", array_map(function($row){ return "<tr><td>" . $row->TABLE_COMMENT . "</td><td>" . $row->TABLE_NAME . "</td><td><a href='table-viewer?table={$row->TABLE_NAME}'>" . trans("admin.lookup") . "</a></td></tr>"; }, $data));
        return $content->body("
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
