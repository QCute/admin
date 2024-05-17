<?php

namespace App\Admin\Controllers\ChargeStatistic;

use App\Admin\Builders\Chart;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\ChargeStatistic\ChargeDistributionModel;
use Illuminate\Http\Request;

class ChargeDistributionController extends StatisticController
{
    public function index(Request $request)
    {
        $begin = $this->getBeginTime($request);
        $end = $this->getEndTime($request);

        $data = ChargeDistributionModel::getRate($begin, $end);

        return (new Chart())
            ->pie($data)
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
