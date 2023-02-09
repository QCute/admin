<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class UserOnlineController extends ChartController
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
        list($before, $now, $active) = $this->getTime("hour");
        if ($active == "day") {
            $step = 3600;
            $format = "H";
            $data = SwitchServerController::getDB()
                ->table("online_log")
                ->whereBetween("time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("AVG(`total`) AS `total`"),
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
                    "total",
                    "online",
                    "hosting",
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%H:%i') AS `date`"),
                ])
                ->get()
                ->toArray();
        }
        // chart data
        if (empty($data)) {
            // x axis data
            $category = [];
            // y axis data
            $total = [];
            $online = [];
            $hosting = [];
            for ($start = $before; $start <= $now; $start += $step) {
                array_push($category, date($format, $start));
                array_push($total, 0);
                array_push($online, 0);
                array_push($hosting, 0);
            }
        } else {
            $category = array_column($data, "date");
            $total = array_column($data, "total");
            $online = array_column($data, "online");
            $hosting = array_column($data, "hosting");
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
                'name' => trans("admin.total"),
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
                'data' => $total
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
                'data' => $online
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
                'data' => $hosting
            ],
        ];
        $option = [
            'grid' => $grid,
            'legend' => $legend,
            'xAxis' => $xAxis,
            'yAxis' => $yAxis,
            'series' => $series,
        ];
        $chart = $this->makeChart($option, $active);
        $tab = $this->makeTimeTab(["hour", "day", "pick"], $active, $chart);
        // draw
        return $content->title("")->body($tab);
    }
}
