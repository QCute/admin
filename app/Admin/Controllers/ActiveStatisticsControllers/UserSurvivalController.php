<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;

class UserSurvivalController extends ChartController
{
    private static function statistics($time, $register): array
    {
        $survival = [0, 0, 0, 0, 0, 0, 0, 0];
        $charge = [0, 0, 0, 0, 0, 0, 0, 0];
        $free = [0, 0, 0, 0, 0, 0, 0, 0];
        foreach ($register as $row) {
            $offset = ($row->date - $time) / 3600;
            if ($offset <= 2 && $offset <= 3) {
                $survival[0] += $row->survival;
                $charge[0] += $row->charge;
                $free[0] += $row->free;
            }
            if ($offset <= 3 && $offset <= 4) {
                $survival[1] += $row->survival;
                $charge[1] += $row->charge;
                $free[1] += $row->free;
            }
            if ($offset <= 4 && $offset <= 5) {
                $survival[2] += $row->survival;
                $charge[2] += $row->charge;
                $free[2] += $row->free;
            }
            if ($offset <= 5 && $offset <= 6) {
                $survival[3] += $row->survival;
                $charge[3] += $row->charge;
                $free[3] += $row->free;
            }
            if ($offset <= 6 && $offset <= 7) {
                $survival[4] += $row->survival;
                $charge[4] += $row->charge;
                $free[4] += $row->free;
            }
            if ($offset <= 7 && $offset <= 8) {
                $survival[5] += $row->survival;
                $charge[5] += $row->charge;
                $free[5] += $row->free;
            }
            if ($offset <= 7 && $offset <= 15) {
                $survival[6] += $row->survival;
                $charge[6] += $row->charge;
                $free[6] += $row->free;
            }

            $survival[7] += $row->survival;
            $charge[7] += $row->charge;
            $free[7] += $row->free;
        }
        return ["survival" => $survival, "charge" => $charge, "free" => $free];
    }

    public function index(Content $content): Content
    {
        list(, , , $tab) = $this->makeTab(["all"], "all");
        // 1.次日存活=（次日登陆总数-次日注册总数）/ 首日注册总数
        // 2.3日存活=（第三日登陆总数-第三日注册总数）/ 首日+第二日注册总数
        // 3.周存活率=（开服第8天登录-开服第8天注册）/ 开服第1周总注册
        // 4.15日存活率=（开服第15天登陆-开服第15天注册）/ 开服15日总注册
        // 5.30日存活率=（开服第30天登陆-开服第30天注册）/ 开服30日总注册
        $before = SwitchServerController::getCurrentServerOpenTime();
        $now = SwitchServerController::getCurrentServerOpenTime() + (30 * 86400);
        $data = SwitchServerController::getDB()
            ->table("role")
            ->whereBetween("register_time", [$before, $now])
            ->groupBy(["date"])
            ->select([
                DB::raw("COUNT(1) AS `number`"),
                DB::raw("COUNT(`first_recharge_time` != 0) AS `charge`"),
                DB::raw("COUNT(`first_recharge_time` = 0) AS `free`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%Y-%m-%d') AS `date`"),
            ])
            ->get()
            ->toArray();
        // server open time offset
        $login = SwitchServerController::getDB()
            ->table("login_log")
            ->whereBetween("time", [$before - 86400, $now])
            ->groupBy(["date"])
            ->select([
                DB::raw("COUNT(1) AS `total`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d') AS `date`"),
            ])
            ->get()
            ->toArray();
        // chart data
        if (empty($data)) {
            $category = array_map(function ($row) {
                return $row . trans("admin.day");
            }, [2, 3, 4, 5, 6, 7, 15, 30]);
            $login = [0, 0, 0, 0, 0, 0, 0, 0];
            $survival = [0, 0, 0, 0, 0, 0, 0, 0];
            $charge = [0, 0, 0, 0, 0, 0, 0, 0];
            $free = [0, 0, 0, 0, 0, 0, 0, 0];
        } else {
            $category = implode(",", array_map(function ($row) {
                return $row . trans("admin.day");
            }, [2, 3, 4, 5, 6, 7, 15, 30]));
            $data = self::statistics($now, $data);
            $login = array_column($login, "total");
            $survival = $data["survival"];
            $charge = $data["charge"];
            $free = $data["free"];
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
                'name' => trans("admin.login"),
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
                'data' => $login,
            ], [
                'name' => trans("admin.survival"),
                'type' => 'bar',
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
                'data' => $survival
            ], [
                'name' => trans("admin.charge"),
                'type' => 'bar',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => '#73DDFF',
                        'borderColor' => '#73DDFF',
                        'lineStyle' => [
                            'width' => 5,
                            'shadowColor' => 'rgba(158,135,255,0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'data' => $charge
            ], [
                'name' => trans("admin.free"),
                'type' => 'bar',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        'color' => '#9E87FF',
                        'borderColor' => '#9E87FF',
                        'lineStyle' => [
                            'width' => 5,
                            'shadowColor' => 'rgba(158,135,255,0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'data' => $free
            ]
        ];
        // chart
        $chart = $this->makeChart([], $legend, $xAxis, $yAxis, $series);
        // draw
        return $content->title("")->body(new Box("", "{$tab}{$chart}"));
    }
}
