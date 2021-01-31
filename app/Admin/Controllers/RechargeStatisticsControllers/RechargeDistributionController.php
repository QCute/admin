<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Admin\Controllers\TimeTabController;
use App\Admin\Controllers\SwitchServerController;

class RechargeDistributionController extends TimeTabController
{
    public function index(Content $content)
    {
        $database = SwitchServerController::getCurrentServer();
        list($before, $now, , $nav) = $this->makeNav(["day", "week", "month", "all", "pick_time"], "day");
        $data = DB::select("SELECT `money` AS `name`, COUNT(1) AS `value` FROM `{$database}`.`recharge` WHERE `time` BETWEEN ? AND ? GROUP BY `recharge_id`", [$before, $now]);
        if (empty($data))
        {
            $color = "color: ['rgba(128, 128, 128, 1)']";
            $data = [["name" => "0", "value" => "0", "label" => ["formatter" => "{b}: {c} (100%)"]]];
        }
        else 
        {
            $color = "color: []";
            $data = array_map(function($object){ $object->label = ["formatter" => "{b}: {c} ({d}%)"]; return $object; }, $data);
        }
        // draw
        return $content->title('')->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '100px', bottom: '200px', containLabel: true },
                    legend: {type: 'scroll', orient: 'vertical', right: 50, top: 50, bottom: 50},
                    series: [{type: 'pie', radius: '60%', center: ['50%', '45%'], {$color}, data: " . json_encode($data) . "}]
                });
            });
            </script>
        ");
    }
}
