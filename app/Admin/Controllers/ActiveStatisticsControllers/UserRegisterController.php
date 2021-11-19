<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Widgets\Box;

class UserRegisterController extends ChartController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        list($before, $now, $current, $tab) = $this->makeTab(["day", "week", "month", "all", "pick"], "day");
        if ($current == "day") {
            $step = 3600;
            $format = "H";
            $data = SwitchServerController::getDB()
                ->table("role")
                ->whereBetween("register_time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("COUNT(1) AS `number`"),
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%H') AS `date`"),
                ])
                ->get()
                ->toArray();
        } else if ($current == "week" || $current == "month") {
            $step = 86400;
            $format = "m-d";
            $data = SwitchServerController::getDB()
                ->table("role")
                ->whereBetween("register_time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("COUNT(1) AS `number`"),
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%m-%d') AS `date`"),
                ])
                ->get()
                ->toArray();
        } else {
            $step = 86400;
            $format = "Y-m-d";
            $data = SwitchServerController::getDB()
                ->table("role")
                ->whereBetween("register_time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("COUNT(1) AS `number`"),
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%Y-%m-%d') AS `date`"),
                ])
                ->get()
                ->toArray();
        }
        // chart data
        if (empty($data)) {
            // x axis data
            $category = [];
            // y axis data
            $register = [];
            for ($start = $before; $start <= $now; $start += $step) {
                array_push($category, date($format, $start));
                array_push($register, 0);
            }
        } else {
            $category = array_column($data, "date");
            $register = array_column($data, "number");
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
                ]
            ],
            'axisLine' => [
                'lineStyle' => [
                    'color' => '#DCE2E8'
                ]
            ]
        ];
        $series = [
            [
                'type' => 'line',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => '#F56948',
                        'borderColor' => '#F56948',
                        'lineStyle' => [
                            'width' => 5,
                            'shadowColor' => 'rgba(158,135,255,0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'data' => $register
            ]
        ];
        $chart = $this->makeChart([], $legend, $xAxis, $yAxis, $series);
        // draw
        return $content->title("")->body(new Box("", "{$tab}{$chart}"));
    }
}
