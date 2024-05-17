<?php

namespace App\Admin\Controllers\ChargeStatistic;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\ChargeStatistic\ArpPuModel;
use Illuminate\Http\Request;

class ArpPuController extends StatisticController
{
    public function index(Request $request)
    {
        $begin = $this->getBeginTime($request);
        $end = $this->getEndTime($request);

        $range = range(2, 30);
        $data = ArpPuModel::getDaily($begin, $end, $range);

        $header = collect([
            (new Header())->field('date')->title(trans('admin.time.date'))->width(120)->align(),
            (new Header())->field('number')->title(trans('admin.statistic.number'))->width(120)->align(),
        ]);

        foreach($range as $day) {
            $field = 'day' . '_' . $day;
            $title = $day . trans('admin.word.gap') . trans('admin.time.day');
            $header[] = (new Header())->field($field)->title($title)->width(120)->align();
        }

        $form = new Form();
        $form->method('GET')->inline()->align('right');
        $form->dateRange('date')->begin(date('Y-m-d', $begin))->end(date('Y-m-d', $end));

        return (new Table())
            ->form($form)
            ->header($header)
            ->data($data)
            ->build();
    }
}
