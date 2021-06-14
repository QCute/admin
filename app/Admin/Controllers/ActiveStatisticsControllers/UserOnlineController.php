<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;

class UserOnlineController extends ChartController
{

    public function index(Content $content): Content
    {
        list($before, $now, $current, $tab) = $this->makeTab(["hour", "day", "pick"], "hour");
        if ($current == "day") {
            $step = 3600;
            $format = "H";
            $data = SwitchServerController::getDB()
                ->table("online_log")
                ->whereBetween("time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("AVG(`all`) AS `all`"),
                    DB::raw("AVG(`online`) AS `online`"),
                    DB::raw("AVG(`hosting`) AS `hosting`"),
                    DB::raw("`hour` AS `date`"),
                ])
                ->get()
                ->toArray();
        } else {
            $step = 60;
            $format = "H-i";
            $data = SwitchServerController::getDB()
                ->table("online_log")
                ->whereBetween("time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    "all",
                    "online",
                    "hosting",
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%H:%i') AS `date`"),
                ])
                ->get()
                ->toArray();
        }
        // x axis data
        $category = [];
        // y axis data
        $all = [];
        $online = [];
        $hosting = [];
        // chart data
        if (empty($data)) {
            for ($start = $before; $start <= $now; $start += $step) {
                array_push($category, date($format, $start));
                array_push($all, 0);
                array_push($online, 0);
                array_push($hosting, 0);
            }
        } else {
            $category = array_column($data, "date");
            $all = array_column($data, "all");
            $online = array_column($data, "online");
            $hosting = array_column($data, "hosting");
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
                'name' => trans("admin.all"),
                'type' => 'line',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => '#F56948',
                        'borderColor' => '#F56948',
                        'lineStyle' => [
                            'width' => 5,
                            'shadowColor' => 'rgba(158,135,255, 0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'data' => $all
            ], [
                'name' => trans("admin.online"),
                'type' => 'line',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => '#73DDFF',
                        'borderColor' => '#73DDFF',
                        'lineStyle' => [
                            'width' => 5,
                            'shadowColor' => 'rgba(158,135,255, 0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'data' => $all
            ], [
                'name' => trans("admin.hosting"),
                'type' => 'line',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => '#9E87FF',
                        'borderColor' => '#9E87FF',
                        'lineStyle' => [
                            'width' => 5,
                            'shadowColor' => 'rgba(158,135,255, 0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'data' => $all
            ],
        ];
        // chart
        $chart = $this->makeChart([], $legend, $xAxis, $yAxis, $series);
        // draw
        return $content->title("")->body(new Box("", "{$tab}{$chart}"));
    }
}
