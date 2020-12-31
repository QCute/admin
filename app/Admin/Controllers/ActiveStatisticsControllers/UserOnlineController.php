<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;
use App\Admin\Controllers\TimeTabController;

class UserOnlineController extends TimeTabController
{
    public function index(Content $content)
    {
        list($before, $now, $current, $nav) = $this->makeNav(array("hour", "day", "pick_time"), "hour");
        if ($current == "day")
        {
            $step = 3600;
            $format = "'H'";
            $data = DB::select("SELECT AVG(`all`) AS `all`, AVG(`online`) AS `online`, AVG(`hosting`) AS `hosting`, `hour` AS `date` FROM " . SwitchServerController::getCurrentServer() . ".`online_log` WHERE `time` BETWEEN " . $before . " AND " . $now . " GROUP BY `date` ");
        }
        else
        {
            $step = 60;
            $format = "'H-i'";
            $data = DB::select("SELECT `all`, `online`, `hosting`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`time`), '%H:%i'), \"'\") AS `date` FROM " . SwitchServerController::getCurrentServer() . ".`online_log` WHERE `time` BETWEEN " . $before . " AND " . $now . "");
        }
        // chart data
        if (empty($data))
        {
            $category = array();
            $all = array();
            $online = array();
            $hosting = array();
            for ($start = $before; $start <= $now; $start += $step)
            {
                array_push($category, date($format, $start));
                array_push($all, 0);
                array_push($online, 0);
                array_push($hosting, 0);
            }
            $category = implode(",", $category);
            $all = implode(",", $all);
            $online = implode(",", $online);
            $hosting = implode(",", $hosting);
        }
        else
        {
            $category = implode(",", array_column($data, "date"));
            $all = implode(",", array_column($data, "all"));
            $online = implode(",", array_column($data, "online"));
            $hosting = implode(",", array_column($data, "hosting"));
        }
        // draw
        return $content->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '20px', bottom: '100px', containLabel: true },
                    legend: { icon: 'circle', top: '5%', right: '0%', itemWidth: 6, itemGap: 20, textStyle: { color: '#556677' } },
                    xAxis: { type: 'category', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } }, data: [{$category}] },
                    yAxis: { type: 'value', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } } },
                    series: [
                        {name: '" . trans("admin.all") . "', type: 'line', smooth: true, itemStyle: { normal: { color: '#F56948', borderColor: '#F56948', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$all}] },
                        {name: '" . trans("admin.online") . "', type: 'line', smooth: true, itemStyle: { normal: { color: '#73DDFF', borderColor: '#73DDFF', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$online}] },
                        {name: '" . trans("admin.hosting") . "', type: 'line', smooth: true, itemStyle: { normal: { color: '#9E87FF', borderColor: '#9E87FF', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$hosting}] },
                    ]
                });
            });
            </script>
        ");
    }
}
