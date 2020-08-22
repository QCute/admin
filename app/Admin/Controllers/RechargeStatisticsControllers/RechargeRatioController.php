<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class RechargeRatioController extends Controller
{
    public function index(Content $content)
    {
        $total = DB::select("SELECT COUNT(*) AS `total` FROM " . SwitchServerController::getCurrentServer() . ".`role`");
        $recharge = DB::select("SELECT COUNT(1) AS `recharge` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `first_recharge_time` > 0");
        if ($recharge[0]->recharge == 0)
        {
            $color = "color: ['rgba(128, 128, 128, 1)']";
            $data = array(array("name" => "0", "value" => "0", "label" => array("formatter" => "{b}: {c} (100%)")));
        }
        else
        {
            $color = "color: []";
            $data = array(array("name" => trans("admin.charge"), "value" => $recharge[0]->recharge, "label" => array("formatter" => "{b}: {c} ({d}%)")), array("name" => trans("admin.free"), "value" => $total[0]->total - $recharge[0]->recharge, "label" => array("formatter" => "{b}: {c} ({d}%)")), );
        }
        // nav
        // $list = implode("", array_map(function($time, $text) { if($time == request()->input("time", 1)) return "<li role='presentation' class='active' ><a>" . trans("admin." . $text) . "</a></li>"; else return "<li role='presentation' ><a href='recharge-distribution?time={$time}'>" . trans("admin." . $text) . "</a></li>"; }, array(1, 7, 30, SwitchServerController::getCurrentServerOpenDays()), array("day", "week", "month", "all")));
        // draw
        return $content->body("
            <style>#app, #pjax-container { height: 100%; overflow: hidden; } </style>
            <style>.content, .content > .row, .content > .row > .col-md-12 { height: 100%; background-color: white; } </style>
            <style>.nav-tabs > li > a { border-radius: unset; } </style>
            <ul class='nav nav-tabs'>
                <li role='presentation' class='active' ><a>" . trans("admin.all") . "</a></li>
            </ul>
            <div id='chart' style='width: 100%; height: 100%; padding-bottom: 100px; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    legend: {type: 'scroll', orient: 'vertical', right: 50, top: 50, bottom: 50},
                    series: [{type: 'pie', {$color}, data: " . json_encode($data) . "}]
                });
            });
            </script>
        ");
    }
}
