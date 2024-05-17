<?php

namespace App\Admin\Controllers\ActiveStatistic;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\ActiveStatistic\UserSurvivalModel;
use Illuminate\Http\Request;

class UserSurvivalController extends StatisticController
{
    public function index(Request $request)
    {
        $begin = $this->getBeginTime($request);
        $end = $this->getEndTime($request);

        $range = range(2, 30);
        $data = UserSurvivalModel::getDaily($begin, $end, $range);

        $header = collect([
            (new Header())->field('date')->title(trans('admin.time.date'))->align()->width(120),
            (new Header())->field('number')->title(trans('admin.statistic.number'))->align()->width(120),
        ]);

        foreach($range as $day) {
            $field = 'day' . '_' . $day;
            $title = $day . trans('admin.word.gap') . trans('admin.time.day');
            $header[] = (new Header())->field($field)->title($title)->align()->width(120);
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
