<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class UserOnlineController extends Controller
{
    public function index(Content $content)
    {
        $now = time();
        $before = $now - intval(request()->input("time", 10)) * 60;
        // $before = strtotime(date("Y-m-d", $now - 60 * intval(request()->input("time", 1)))) + 60;
        $data = DB::select("SELECT `all`, `online`, `hosting`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d %H:%i'), \"'\") AS `date` FROM " . SwitchServerController::getCurrentServer() . ".`online_log` WHERE `time` BETWEEN " . $before . " AND " . $now . "");
        // chart data
        if (count($data) == 0)
        {
            $category = array();
            $all = array();
            // $online = array();
            // $hosting = array();
            for ($start = $before; $start <= $now; $start += 86400)
            {
                array_push($category, date("'m-d'", $start));
                array_push($all, 0);
                // array_push($online, 0);
                // array_push($hosting, 0);
            }
            $category = implode(",", $category);
            $all = implode(",", $all);
            // $online = implode(",", $online);
            // $hosting = implode(",", $hosting);
        }
        else
        {
            $category = implode(",", array_column($data, "date"));
            $all = implode(",", array_column($data, "all"));
            // $online = implode(",", array_column($data, "online"));
            // $hosting = implode(",", array_column($data, "hosting"));
        }
        // nav
        $list = implode("", array_map(function($time, $text) { if($time == request()->input("time", 10)) return "<li role='presentation' class='active' ><a>" . trans("admin." . $text) . "</a></li>"; else return "<li role='presentation' ><a href='user-online?time={$time}'>" . trans("admin." . $text) . "</a></li>"; }, array(10, 60, 60 * 24), array("minute", "hour", "day")));
        // draw
        return $content->body("
            <style>#app, #pjax-container { height: 100%; overflow: hidden; } </style>
            <style>.content, .content > .row, .content > .row > .col-md-12 { height: 100%; background-color: white; } </style>
            <style>.nav-tabs > li > a { border-radius: unset; } </style>
            <ul class='nav nav-tabs'>
                {$list}
            </ul>
            <div id='chart' style='width: 100%; height: 100%; padding-bottom: 100px; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    xAxis: {type: 'category', data: [{$category}]},
                    yAxis: {type: 'value'},
                    grid:{left: '0px', right: '0px', bottom: '0px', containLabel: true},
                    series: [{type: 'line', smooth: true, data: [{$all}]}]
                });
            });
            </script>
        ");
    }
}
