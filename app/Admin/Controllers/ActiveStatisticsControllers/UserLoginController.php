<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class UserLoginController extends ChartController
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
        if ($active == "day")  {
            $step = 3600;
            $format = "H";
            $data = SwitchServerController::getDB()
                ->table("login_log")
                ->whereBetween("login_time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("COUNT(DISTINCT `role_id`) AS `number`"),
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`login_time`), '%H') AS `date`")
                ])
                ->get()
                ->toArray();
        } else if ($active == "week" || $active == "month") {
            $step = 86400;
            $format = "m-d";
            $data = SwitchServerController::getDB()
                ->table("login_log")
                ->whereBetween("time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("COUNT(DISTINCT `role_id`) AS `number`"),
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d') AS `date`")
                ])
                ->get()
                ->toArray();
        } else {
            $step = 86400;
            $format = "Y-m-d";
            $data = SwitchServerController::getDB()
                ->table("login_log")
                ->whereBetween("time", [$before, $now])
                ->groupBy(["date"])
                ->select([
                    DB::raw("COUNT(DISTINCT `role_id`) AS `number`"),
                    DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') AS `date`")
                ])
                ->get()
                ->toArray();
        }
        // chart data
        if (empty($data)) {
            // x axis data
            $category = [];
            // y axis data
            $login = [];
            for ($start = $before; $start <= $now; $start += $step) {
                array_push($category, date($format, $start));
                array_push($login, 0);
            }
        } else {
            $category = array_column($data, "date");
            $login = array_column($data, "number");
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
                'data' => $login
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
