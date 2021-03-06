<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;

class FirstRechargeTimeDistributionController extends ChartController
{

    private static function slice($data): array
    {
        $time = [
            (object)[
                "name" => "1" . trans("admin.day"),
                "value" => 0,
            ], (object)[
                "name" => "3" . trans("admin.day"),
                "value" => 0,
            ], (object)[
                "name" => "5" . trans("admin.day"),
                "value" => 0,
            ], (object)[
                "name" => "7" . trans("admin.day"),
                "value" => 0,
            ], (object)[
                "name" => "15" . trans("admin.day"),
                "value" => 0,
            ], (object)[
                "name" => "30" . trans("admin.day"),
                "value" => 0,
            ],
        ];
        foreach ($data as $row) {
            if ($row->name <= 1) {
                $time[0]->value += 1;
            } else if ($row->name <= 3) {
                $time[1]->value += 1;
            } else if ($row->name <= 5) {
                $time[2]->value += 1;
            } else if ($row->name <= 7) {
                $time[3]->value += 1;
            } else if ($row->name <= 15) {
                $time[4]->value += 1;
            } else {
                $time[5]->value += 1;
            }
        }
        return $time;
    }

    public function index(Content $content): Content
    {
        list(, , , $tab) = $this->makeTab(["all"], "all");
        $data = SwitchServerController::getDB()
            ->table("role")
            ->where("first_recharge_time", ">", "0")
            ->groupBy(["name"])
            ->select([
                DB::raw("(`first_recharge_time` - `register_time`) div 86400 + 1 AS `name`"),
                DB::raw("COUNT(1) AS `value`"),
            ])
            ->get()
            ->toArray();
        // merge
        $data = self::slice($data);
        if (empty($data)) {
            $color = ['rgba(128, 128, 128, 1)'];
            $data = [['name' => '0', 'value' => '0', 'label' => ['formatter' => '{b}: {c} (100%)']]];
        } else {
            $color = ['#37a2da', '#32c5e9', '#9fe6b8', '#ffdb5c', '#ff9f7f', '#fb7293', '#e7bcf3', '#8378ea'];
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
