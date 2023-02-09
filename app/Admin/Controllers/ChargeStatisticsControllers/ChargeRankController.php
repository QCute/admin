<?php

namespace App\Admin\Controllers\ChargeStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class ChargeRankController extends ChartController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        // check
        $server = SwitchServerController::getCurrentServer();
        if (empty($server)) {
            return $content
                ->title($this->title())
                ->withWarning("Could not found current server");
        }
        // view
        list($before, $now, $active) = $this->getTime("day");
        $data = SwitchServerController::getDB()
            ->table("role")
            ->where("charge_total", ">", 0)
            ->whereBetween("register_time", [$before, $now])
            ->groupBy(["name"])
            ->orderBy("charge_total", "DESC")
            ->limit(100)
            ->select([
                DB::raw("`charge_total` AS `value`"),
                DB::raw("`role_name` AS `name`"),
            ])
            ->get()
            ->toArray();
        // chart data
        if (empty($data)) {
            $category = [trans("admin.nothing")];
            $rank = [""];
        } else {
            $category = array_column($data, "name");
            $rank = array_column($data, "value");
        }
        // chart
        $grid = [
            'left' => '0px',
            'right' => '0px',
            'top' => '25px',
            'bottom' => '0px',
            'containLabel' => true
        ];
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
        $option = [
            'grid' => $grid,
            'xAxis' => $xAxis,
            'yAxis' => $yAxis,
            'series' => $series,
        ];
        $chart = $this->makeChart($option, $active);
        $tab = $this->makeTimeTab(["day", "week", "month", "all", "pick"], $active, $chart);
        // draw
        return $content->title("")->body($tab);
    }
}
