<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Admin\Controllers\TimeTabController;
use App\Admin\Controllers\SwitchServerController;

class RechargeRankController extends TimeTabController
{
    public function index(Content $content)
    {
        $database = SwitchServerController::getCurrentServer();
        list($before, $now, $current, $nav) = $this->makeNav(["day", "week", "month", "all", "pick_time"], "day");
        $data = DB::select("SELECT `recharge_total` AS `value`, `role_name` AS `name` FROM `{$database}`.`role` WHERE `register_time` BETWEEN ? AND ? ORDER BY `recharge_total` DESC LIMIT 100", [$before, $now]);
        // chart data
        if (empty($data))
        {
            $category = "'无'";
            $rank = "0";
        }
        else 
        {
            $category = "'" . implode("','", array_column($data, "name")) . "'";
            $rank = implode(",", array_column($data, "value"));
        }
        // draw
        return $content->title('')->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            function format(value) {
                if (value >= " . trans("admin.unit") . ") { 
                    return (value / " . trans("admin.unit") . ").toLocaleString() + '" . trans("admin.unit_name") . "'; 
                } else { 
                    return value.toLocaleString(); 
                }
            }
            // dataZoom: [{ type: 'inside', startValue: 0, endValue: 7, zoomLock: true }]
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '20px', bottom: '100px', containLabel: true },
                    xAxis: { type: 'value', splitLine: { show: true }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { show: false, lineStyle: { color: '#DCE2E8' } } },
                    yAxis: [
                        { type: 'category', inverse: true, splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { show: false }, data: [{$category}] },
                        { inverse: true, axisTick: 'none', axisLine: 'none', show: true, axisLabel: { textStyle: { color: '#556677' }, formatter: function(value) { return format(value); } }, data: [{$rank}] }
                    ],
                    series: [{ type: 'bar', barWidth: 3, itemStyle: { normal: { color: '#37a2da' } }, data: " . json_encode($data) . " }]
                });
            });
            </script>
        ");
    }
}
