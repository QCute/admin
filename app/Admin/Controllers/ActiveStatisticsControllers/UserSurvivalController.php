<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class UserSurvivalController extends ChartController
{
    private static function getLogin($before, $now): int
    {
        $login = SwitchServerController::getDB()
            ->table("login_log")
            ->whereBetween("time", [$before, $now])
            ->select([
                DB::raw("COUNT(DISTINCT `role_id`) AS `login`"),
                DB::raw("UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d')) AS `date`"),
            ])
            ->get()
            ->toArray();
        return $login ? $login[0]->login : 0;
    }

    private static function getRegister($before, $now): int
    {
        $register = SwitchServerController::getDB()
            ->table("role")
            ->whereBetween("register_time", [$before, $now])
            ->select([
                DB::raw("COUNT(1) AS `register`"),
                DB::raw("UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%Y-%m-%d')) AS `date`"),
            ])
            ->get()
            ->toArray();
        return $register ? $register[0]->register : 0;
    }

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
        list(, , $active) = $this->getTime("all");
        // 当日存活 = (当日登录总数 - 当日注册总数) / 开服到昨日注册总数
        $before = SwitchServerController::getCurrentServerOpenTime();
        $category = [2, 3, 4, 5, 6, 7, 15, 30];
        // all
        $data = array_map( function ($row) use ($before) {
            $currentLogin = self::getLogin($before + ($row - 1 ) * 86400, $before + $row * 86400);
            $currentRegister = self::getRegister($before + ($row - 1 ) * 86400, $before + $row * 86400);
            $currentTotal = self::getRegister($before, $before + $row * 86400);
            return $currentTotal == 0 ? 0: (($currentLogin - $currentRegister) / $currentTotal) * 100;
        }, $category);
        // category
        $category = array_map(function ($row) {
            return $row . trans("admin.day");
        }, [2, 3, 4, 5, 6, 7, 15, 30]);
        // data
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
                'type' => 'bar',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => '#00B400',
                        'borderColor' => '#28B416',
                        'lineStyle' => [
                            'width' => 5,
                            'shadowColor' => 'rgba(158,135,255, 0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'data' => $data,
            ],
        ];
        // chart
        $chart = $this->makeChart([], $legend, $xAxis, $yAxis, $series);
        $tab = $this->makeTab(["all"], $active, $chart);
        // draw
        return $content->title("")->body($tab);
    }
}
