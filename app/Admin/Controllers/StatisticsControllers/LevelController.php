<?php

namespace App\Admin\Controllers\StatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class LevelController extends ChartController
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
        list($before, $now, $active) = $this->getTime("all");

        $data = SwitchServerController::getDB()
            ->table("role")
            ->groupBy("level")
            ->orderBy("level", "ASC")
            ->select([
                "level",
                DB::raw("COUNT(`role_id`) AS `number`"),
            ])
            ->get()
            ->toArray();

        // chart data
        if (empty($data)) {
            $category = [
                1  . trans("admin.level_name"),
                2  . trans("admin.level_name"),
                3  . trans("admin.level_name"),
                4  . trans("admin.level_name"),
                5  . trans("admin.level_name"),
                6  . trans("admin.level_name"),
                7  . trans("admin.level_name"),
                8  . trans("admin.level_name"),
                9  . trans("admin.level_name"),
                10  . trans("admin.level_name"),
            ];
            $totalData = [];
            $numberData = [];
        } else {
            $category = [];
            $totalData = [];
            $numberData = [];
            foreach($data as $row) {
                $category[] = $row->level . trans("admin.level_name");
                $numberData[] = $row->number;
                // acc add
                foreach ($totalData as &$item) {
                    $item += $row->number;
                }
                $totalData[] = $row->number;
            }
        }

        // grid
        $grid = [
            'left' => '0px',
            'right' => '50px',
            'top' => '50px',
            'bottom' => '50px',
            'containLabel' => true
        ];
        // legend
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
        $yAxis = [
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
        $series = [
            [
                'name' => trans("admin.number"),
                'type' => 'bar',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        // 'color' => '#00B400',
                        // 'borderColor' => '#28B416',
                        'lineStyle' => [
                            'width' => 5,
                            // 'shadowColor' => 'rgba(158,135,255, 0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'label' => [
                    'show' => true,
                    'position' => 'right',
                ],
                'data' => $numberData,
            ],
            [
                'name' => trans("admin.total"),
                'type' => 'bar',
                'smooth' => true,
                'itemStyle' => [
                    'normal' => [
                        // 'color' => '#00B400',
                        // 'borderColor' => '#28B416',
                        'lineStyle' => [
                            'width' => 5,
                            // 'shadowColor' => 'rgba(158,135,255, 0.3)',
                            'shadowBlur' => 10,
                            'shadowOffsetY' => 20
                        ]
                    ]
                ],
                'label' => [
                    'show' => true,
                    'position' => 'right',
                ],
                'data' => $totalData,
            ],
        ];

        $option = [
            'grid' => $grid,
            'legend' => $legend,
            'xAxis' => $xAxis,
            'yAxis' => $yAxis,
            'series' => $series,
        ];
        $chart = $this->makeChart($option, $active, 164, count($totalData) * 24);

        // time tab
        $tab = $this->makeTimeTab(["all"], $active, "$chart");
        // draw
        return $content->title("")->body($tab);
    }
}
