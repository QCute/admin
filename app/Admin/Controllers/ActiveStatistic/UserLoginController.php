<?php

namespace App\Admin\Controllers\ActiveStatistic;

use App\Admin\Builders\Chart;
use App\Admin\Builders\Form;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\ActiveStatistic\UserLoginModel;
use Illuminate\Http\Request;

class UserLoginController extends StatisticController
{
    public function index(Request $request)
    {
        $begin = $this->getBeginTime($request);
        $end = $this->getEndTime($request);

        $data = ($end - $begin <= 86400) ? UserLoginModel::getDayAvg($begin, $end) : UserLoginModel::getAvg($begin, $end);

        $category = $this->getCategory($request);
        $value = $this->getValue($request);

        $form = new Form();
        $form->method('GET')->inline()->align('right');
        $form->dateRange('date')->begin(date('Y-m-d', $begin))->end(date('Y-m-d', $end));

        return (new Chart())
            ->form($form)
            ->key('date')
            ->value([
                'number' => trans('admin.statistic.number'),
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
