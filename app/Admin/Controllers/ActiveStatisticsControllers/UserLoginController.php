<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;
use App\Admin\Controllers\TimeTabController;

class UserLoginController extends TimeTabController
{
    public function index(Content $content)
    {
        list($before, $now, $current, $nav) = $this->makeNav(array("day", "week", "month", "all", "pick_time"), "day");
        if ($current == "day")
        {
            $step = 3600;
            $format = "'H'";
            $data = DB::select("SELECT COUNT(1) AS `number`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`login_time`), '%H'), \"'\") AS `date` FROM " . SwitchServerController::getCurrentServer() . ".`login_log` WHERE `login_time` BETWEEN " . $before . " AND " . $now . " GROUP BY `date`");
        }
        else
        {
            $step = 86400;
            $format = "'m-d'";
            $data = DB::select("SELECT COUNT(1) AS `number`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d'), \"'\") AS `date` FROM " . SwitchServerController::getCurrentServer() . ".`login_log` WHERE `time` BETWEEN " . $before . " AND " . $now . "  GROUP BY `date`");
        }
        // chart data
        if (empty($data))
        {
            $category = array();
            $login = array();
            for ($start = $before; $start <= $now; $start += $step)
            {
                array_push($category, date($format, $start));
                array_push($login, 0);
            }
            $category = implode(",", $category);
            $login = implode(",", $login);
        }
        else
        {
            $category = implode(",", array_column($data, "date"));
            $login = implode(",", array_column($data, "number"));
        }
        // draw
        return $content->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '20px', bottom: '100px', containLabel: true },
                    legend: { icon: 'circle', top: '5%', right: '0%', itemWidth: 6, itemGap: 20, textStyle: { color: '#556677' }},
                    xAxis: { type: 'category', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } }, data: [{$category}] },
                    yAxis: { type: 'value', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } } },
                    series: [{ type: 'line', smooth: true, itemStyle: { normal: { color: '#F56948', borderColor: '#F56948', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$login}] }]
                });
            });
            </script>
        ");
    }
}