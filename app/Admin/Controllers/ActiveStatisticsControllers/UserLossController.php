<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class UserLossController extends Controller
{
    public function index(Content $content)
    {
        $now = strtotime(date("Y-m-d", time() - 86400 * intval(request()->input("time", 1)))) + 86400;
        $before = strtotime(date("Y-m-d", $now - 86400 * 2 * intval(request()->input("time", 1)))) + 86400;
        $data = DB::select("SELECT COUNT(1) as `number`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`online_time`), '%Y-%m-%d'), \"'\") as `date` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `online_time` BETWEEN " . $before . " AND " . $now . " GROUP BY `date`");
        // chart data
        if (count($data) == 0)
        {
            $category = array();
            $loss = array();
            for ($start = $before; $start <= $now; $start += 86400)
            {
                array_push($category, date("'Y-m-d'", $start));
                array_push($loss, 0);
            }
            $category = implode(",", $category);
            $loss = implode(",", $loss);
        }
        else
        {
            $category = implode(",", array_column($data, "date"));
            $loss = implode(",", array_column($data, "number"));
        }
        // nav
        $list = implode("", array_map(function($time, $text) { if($time == request()->input("time", 1)) return "<li role='presentation' class='active' ><a>" . trans("admin." . $text) . "</a></li>"; else return "<li role='presentation' ><a href='user-loss?time={$time}'>" . trans("admin." . $text) . "</a></li>"; }, array(1, 7, 30, SwitchServerController::getCurrentServerOpenDays()), array("day", "week", "month", "all")));
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
                    series: [{type: 'line', smooth: true, data: [{$loss}]}]
                });
            });
            </script>
        ");
    }
}
