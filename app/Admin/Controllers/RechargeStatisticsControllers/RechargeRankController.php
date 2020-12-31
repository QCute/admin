<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;
use App\Admin\Controllers\TimeTabController;

class RechargeRankController extends TimeTabController
{
    public function index(Content $content)
    {
        list($before, $now, $current, $nav) = $this->makeNav(array("day", "week", "month", "all", "pick_time"), "day");
        $data = DB::select("SELECT `recharge_total` AS `value`, `role_name` AS `name` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `register_time` BETWEEN " . $before . " AND " . $now . " ORDER BY `recharge_total` DESC LIMIT 100");
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
        return $content->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            // dataZoom: [{ type: 'inside', startValue: 0, endValue: 7, zoomLock: true }]
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '20px', bottom: '100px', containLabel: true },
                    xAxis: { type: 'value', splitLine: { show: true }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { show: false, lineStyle: { color: '#DCE2E8' } } },
                    yAxis: [
                        { type: 'category', inverse: true, splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { show: false }, data: [{$category}] },
                        { inverse: true, axisTick: 'none', axisLine: 'none', show: true, axisLabel: { textStyle: { color: '#556677' }, formatter: function(value) { if (value >= " . trans("admin.unit") . ") { return (value / " . trans("admin.unit") . ").toLocaleString() + '" . trans("admin.unit_name") . "'; } else { return value.toLocaleString(); } } }, data: [{$rank}] }
                    ],
                    series: [{ type: 'bar', barWidth: 3, itemStyle: { normal: { color: '#37a2da' } }, data: " . json_encode($data) . " }]
                });
            });
            </script>
        ");
    }
}
