<?php

namespace App\Admin\Controllers\RechargeStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;

class RechargeRankController extends ChartController
{
    public function index(Content $content): Content
    {
        list($before, $now, , $tab) = $this->makeTab(["day", "week", "month", "all", "pick"], "day");
        $data = SwitchServerController::getDB()
            ->table("role")
            ->where("recharge_total", ">", 0)
            ->whereBetween("register_time", [$before, $now])
            ->groupBy(["name"])
            ->orderBy("recharge_total", "DESC")
            ->limit(100)
            ->select([
                DB::raw("`recharge_total` AS `value`"),
                DB::raw("`role_name` AS `name`"),
            ])
            ->get()
            ->toArray();
        // chart data
        if (empty($data)) {
            $category = ["无"];
            $rank = [""];
        } else {
            $category = array_column($data, "name");
            $rank = array_column($data, "value");
        }
        $xAxis = [
            'type'=> 'value',
            'splitLine'=> [
                'show'=> true
            ],
            'axisTick'=> [
                'show'=> false
            ],
            'axisLabel'=> [
                'textStyle'=> [
                    'color'=> '#556677'
                ]
            ],
            'axisLine'=> [
                'show'=> false,
                'lineStyle'=> [
                    'color'=> '#DCE2E8'
                ]
            ]
        ];
        $yAxis = [
            [
                'type'=> 'category',
                'inverse'=> true,
                'splitLine'=> [
                    'show'=> false
                ],
                'axisTick'=> [
                    'show'=> false
                ],
                'axisLabel'=> [
                    'textStyle'=> [
                        'color'=> '#556677'
                    ]
                ],
                'axisLine'=> [
                    'show'=> false
                ],
                'data'=> $category
            ], [
                'inverse'=> true,
                'axisTick'=> 'none',
                'axisLine'=> 'none',
                'show'=> true,
                'axisLabel'=> [
                    'textStyle'=> [
                        'color'=> '#556677'
                    ],

                ],
                'data'=> $rank
            ]
        ];
        $series = [
            'type' => 'bar',
            'barWidth' => '5',
            'itemStyle' => [
                'normal' => [
                    'color' => '#37a2da'
                ]
            ],
            'data' => $data
        ];
        $chart = $this->makeChart([], null, $xAxis, $yAxis, $series);
        // draw
        return $content->title("")->body(new Box("", "{$tab}{$chart}"));
    }
}
