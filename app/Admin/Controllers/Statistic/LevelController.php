<?php

namespace App\Admin\Controllers\Statistic;

use App\Admin\Builders\Chart;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\Statistic\LevelModel;
use Illuminate\Http\Request;

class LevelController extends StatisticController
{
    public function index(Request $request)
    {
        $data = LevelModel::getRank(3);

        $category = $data->pluck('level');
        $value = $this->getValue($request);

        return (new Chart())
            ->key('level')
            ->value([
                'number' => trans('admin.statistic.number'),
            ])
            ->bar($data->reverse())
            ->yAxis('category', $category)
            ->xAxis('value', $value)
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
