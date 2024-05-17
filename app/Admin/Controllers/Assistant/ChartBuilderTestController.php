<?php

namespace App\Admin\Controllers\Assistant;

use App\Admin\Controllers\Controller;
use App\Admin\Builders\Chart;

class ChartBuilderTestController extends Controller
{
    public function index()
    {

        $data = [
            ['date' => '2021-01-01', 'total' => 100, 'origin' => 200, 'target' => 300],
            ['date' => '2021-01-02', 'total' => 100, 'origin' => 200, 'target' => 300],
        ];

        $category = collect($data)->pluck('date');
        $value = collect($data)->pluck('total');

        return (new Chart())
            ->key('date')
            ->value([
                'total' => trans('admin..total'),
                'origin' => trans('admin..origin'), 
                'target' => trans('admin..target'),
            ])
            ->line($data)
            ->xAxis('category', $category)
            ->yAxis('value', $value)
            ->grid(32, 32, 32, 32)
            ->legend(48, 48, null, null, [
                'orient' => 'vertical',
                'itemGap' => 20,
            ])
            ->color([
                '#0090FF',
                '#36CE9E',
                '#FFC005',
                '#FF515A',
                '#8B5CFF',
                '#00CA69'
            ])
            ->build();
    }
}
