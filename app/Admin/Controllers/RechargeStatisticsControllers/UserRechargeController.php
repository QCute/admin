<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class UserRechargeController extends Controller
{
    public function index(Content $content)
    {
        $now = time();
        $before = strtotime(date("Y-m-d", $now - 86400 * intval(request()->input("time", 1)))) + 86400;
        $data = DB::select("SELECT sum(`money`) as `value`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d'), \"'\") as `name` FROM " . SwitchServerController::getCurrentServer() . ".`recharge` WHERE `time` BETWEEN " . $before . " AND " . $now . " GROUP BY `name`");
        $data = array_map(function($object){ $object->label = array("normal" => array("show" => true, "position" => "top", "formatter" => "{c}")); return $object; }, $data);
        // chart data
        if (count($data) == 0) 
        {
            $category = array();
            for ($start = $before; $start <= $now; $start += 86400) 
            {
                array_push($category, date("'m-d'", $start));
                array_push($data, array("value" => 0, "label" => array("normal" => array("show" => true, "position" => "top", "formatter" => "{c}"))));
            }
            $category = implode(",", $category);
        }
        else 
        {
            $category = implode(",", array_column($data, "name"));
        }
        // nav
        $list = implode("", array_map(function($time, $text) { if($time == request()->input("time", 1)) return "<li role='presentation' class='active' ><a>" . trans("admin." . $text) . "</a></li>"; else return "<li role='presentation' ><a href='user-recharge?time={$time}'>" . trans("admin." . $text) . "</a></li>"; }, array(1, 7, 30, SwitchServerController::getCurrentServerOpenDays()), array("day", "week", "month", "all")));
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
                    series: [{type: 'bar', itemStyle: {normal: {color: 'rgba(224, 0, 0, 0.5)', borderWidth:2, borderColor: 'rgba(224, 0, 0, 0.8)'}}, data: " . json_encode($data) . "}]
                });
            });
            </script>
        ");
    }
}
