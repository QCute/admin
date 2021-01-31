<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Admin\Controllers\TimeTabController;
use App\Admin\Controllers\SwitchServerController;

class FirstRechargeTimeDistributionController extends TimeTabController
{
    private static function slice($object)
    {
        if ($object->name <= 1)
            $object->name = "1" . trans("admin.day");
        else if ($object->name <= 3)
            $object->name = "3" . trans("admin.day");
        else if ($object->name <= 7)
            $object->name = "7" . trans("admin.day");
        else if ($object->name <= 15)
            $object->name = "15" . trans("admin.day");
        else
            $object->name = "30" . trans("admin.day");

        return $object;
    }

    public function index(Content $content)
    {
        $database = SwitchServerController::getCurrentServer();
        list(, , , $nav) = $this->makeNav(["all"], "all");
        $data = DB::select("SELECT (`first_recharge_time` - `register_time`) div 86400 + 1 AS `name`, COUNT(1) AS `value` FROM `{$database}`.`role` where `first_recharge_time` > 0 GROUP BY `name`");
        if (empty($data))
        {
            $color = "color: ['rgba(128, 128, 128, 1)']";
            $data = [["name" => "0", "value" => "0", "label" => ["formatter" => "{b}: {c} (100%)"]]];
        }
        else
        {
            $color = "color: ['#37a2da','#32c5e9','#9fe6b8','#ffdb5c','#ff9f7f','#fb7293','#e7bcf3','#8378ea']";
            $data = array_map(function($object){ $object->label = ["formatter" => "{b}: {c} ({d}%)"]; $object->name .= trans("admin.day"); return $object; }, $data);
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
