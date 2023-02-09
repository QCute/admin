<?php

namespace App\Admin\Controllers\ChargeStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class DailyChargeController extends ChartController
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
            ->table("charge")
            ->whereBetween("time", [$before, $now])
            ->groupBy(["name"])
            ->select([
                DB::raw("sum(`money`) AS `value`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d') AS `name`"),
            ])
            ->get()
            ->toArray();
        // chart data
        if (empty($data)) {
            // data
            $category = [];
            for ($start = $before; $start <= $now; $start += 86400) {
                array_push($category, date("m-d", $start));
            }
        } else {
            $category = array_column($data, "name");
        }
        // chart
        $grid = [
            'left' => '0px',
            'right' => '0px',
            'top' => '25px',
            'bottom' => '0px',
            'containLabel' => true
        ];
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
        $option = [
            'grid' => $grid,
            'legend' => $legend,
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
