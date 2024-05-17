<?php

namespace App\Admin\Controllers\Statistic;

use App\Admin\Builders\Chart;
use App\Admin\Builders\Form;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\Statistic\AssetProduceModel;
use Illuminate\Http\Request;

class AssetProduceController extends StatisticController
{
    public function index(Request $request)
    {
        $begin = $this->getBeginTime($request);
        $end = $this->getEndTime($request);

        $asset = $request->input('asset');

        $data = AssetProduceModel::getRate($begin, $end, $asset);

        $form = new Form();
        $form->method('GET')->inline()->align('right');
        $form->dateRange('date')->begin(date('Y-m-d', $begin))->end(date('Y-m-d', $end));

        return (new Chart())
            ->form($form)
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
