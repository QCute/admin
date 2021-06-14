<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;

class DailyRechargeController extends ChartController
{
    public function index(Content $content): Content
    {
        list($before, $now, , $tab) = $this->makeTab(["day", "week", "month", "all", "pick"], "day");
        $data = SwitchServerController::getDB()
            ->table("recharge")
            ->whereBetween("time", [$before, $now])
            ->groupBy(["name"])
            ->select([
                DB::raw("sum(`money`) AS `value`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d') AS `name`"),
            ])
            ->get()
            ->toArray();
        // data
        $category = [];
        // chart data
        if (empty($data)) {
            for ($start = $before; $start <= $now; $start += 86400) {
                array_push($category, date("m-d", $start));
            }
        } else {
            $category = array_column($data, "name");
        }
        $legend = [
            'icon' => 'circle',
            'top' => '5%',
            'right' => '0%',
            'itemWidth' => 6,
            'itemGap' => 20,
            'textStyle' => [
                'color' => '#556677'
            ]
        ];
        $xAxis = [
            'type' => 'category',
            'splitLine' => [
                'show' => false
            ],
            'axisTick' => [
                'show' => false
            ],
            'axisLabel' => [
                'textStyle' => [
                    'color' => '#556677'
                ]
            ],
            'axisLine' => [
                'lineStyle' => [
                    'color' => '#DCE2E8'
                ]
            ],
            'data' => $category
        ];
        $yAxis = [
            'type' => 'value',
            'splitLine' => [
                'show' => false
            ],
            'axisTick' => [
                'show' => false
            ],
            'axisLabel' => [
                'textStyle' => [
                    'color' => '#556677'
                ],

            ],
            'axisLine' => [
                'lineStyle' => [
                'color' => '#DCE2E8'
                ]
            ]
        ];
        $series = [
            [
                'type' => 'bar',
                'itemStyle' => [
                    'normal' => [
                        'color' => '#37a2da'
                    ]
                ]
            ], [
                'label' => [
                    'normal' => [
                        'show' => true,
                        'position' => 'top',

                    ]
                ],
                'data' => $data
            ]
        ];
        $chart = $this->makeChart([], $legend, $xAxis, $yAxis, $series);
        // draw
        return $content->title("")->body(new Box("", "{$tab}{$chart}"));
    }
}
