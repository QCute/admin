<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;
use App\Admin\Controllers\TimeTabController;

class RechargeRatioController extends TimeTabController
{
    public function index(Content $content)
    {
        list($before, $now, , $nav) = $this->makeNav(array("all", "pick_time"), "all");
        $total = DB::select("SELECT COUNT(*) AS `total` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `register_time` BETWEEN " . $before . " AND " . $now . "");
        $recharge = DB::select("SELECT COUNT(1) AS `recharge` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `register_time` BETWEEN " . $before . " AND " . $now . " AND `first_recharge_time` > 0");
        if ($recharge[0]->recharge == 0)
        {
            $color = "color: ['rgba(128, 128, 128, 1)']";
            $data = array(array("name" => "0", "value" => "0", "label" => array("formatter" => "{b}: {c} (100%)")));
        }
        else
        {
            $color = "color: ['#37a2da','#32c5e9','#9fe6b8','#ffdb5c','#ff9f7f','#fb7293','#e7bcf3','#8378ea']";
            $data = array(array("name" => trans("admin.charge"), "value" => $recharge[0]->recharge, "label" => array("formatter" => "{b}: {c} ({d}%)")), array("name" => trans("admin.free"), "value" => $total[0]->total - $recharge[0]->recharge, "label" => array("formatter" => "{b}: {c} ({d}%)")), );
        }
        // draw
        return $content->body("
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
