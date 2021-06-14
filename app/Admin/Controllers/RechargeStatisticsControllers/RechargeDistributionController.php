<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;

class RechargeDistributionController extends ChartController
{
    public function index(Content $content): Content
    {
        list($before, $now, , $tab) = $this->makeTab(["day", "week", "month", "all", "pick"], "day");
        $data = SwitchServerController::getDB()
            ->table("recharge")
            ->whereBetween("time", [$before, $now])
            ->groupBy(["name"])
            ->select([
                DB::raw("`money` AS `name`"),
                DB::raw("COUNT(1) AS `value`"),
            ])
            ->get()
            ->toArray();
        if (empty($data)) {
            $color = ['rgba(128, 128, 128, 1)'];
            $data = [['name' => '0', 'value' => '0', 'label' => ['formatter' => '{b}: {c} (100%)']]];
        } else {
            $color = ['#37a2da','#32c5e9','#9fe6b8','#ffdb5c','#ff9f7f','#fb7293','#e7bcf3','#8378ea'];
            $data = array_map(function ($object) {
                $object->label = ['formatter' => '{b}: {c} ({d}%)'];
                return $object;
            }, $data);
        }
        $legend = [
            'type' => 'scroll',
            'orient' => 'vertical',
            'top' => '50',
            'right' => '50',
            'bottom' => '50'
        ];
        $series = [
            'type' => 'pie',
            'radius' => '60%',
            'center' => ['50%', '45%'],
            'color' => $color,
            'data' => $data
        ];
        $chart = $this->makeChart([], $legend, [], [], $series);
        // draw
        return $content->title("")->body(new Box("", "{$tab}{$chart}"));
    }
}
