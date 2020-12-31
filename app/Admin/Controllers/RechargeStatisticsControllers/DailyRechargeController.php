<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;
use App\Admin\Controllers\TimeTabController;

class DailyRechargeController extends TimeTabController
{
    public function index(Content $content)
    {
        list($before, $now, $current, $nav) = $this->makeNav(array("day", "week", "month", "all", "pick_time"), "day");
        $data = DB::select("SELECT sum(`money`) AS `value`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d'), \"'\") AS `name` FROM " . SwitchServerController::getCurrentServer() . ".`recharge` WHERE `time` BETWEEN " . $before . " AND " . $now . " GROUP BY `name`");
        $data = array_map(function($object){ $object->label = array("normal" => array("show" => true, "position" => "top", "formatter" => "{c}")); return $object; }, $data);
        // chart data
        if (empty($data))
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
        // draw
        return $content->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '20px', bottom: '100px', containLabel: true },
                    legend: { icon: 'circle', top: '5%', right: '5%', itemWidth: 6, itemGap: 20, textStyle: { color: '#556677' }},
                    xAxis: { type: 'category', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } }, data: [{$category}] },
                    yAxis: { type: 'value', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } } },
                    series: [{ type: 'bar', itemStyle: { normal: { color: '#37a2da' } }, data: " . json_encode($data) . " }]
                });
            });
            </script>
        ");
    }
}
