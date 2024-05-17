<?php

namespace App\Admin\Controllers\ChargeStatistic;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\ChargeStatistic\ChargeRankModel;
use Illuminate\Http\Request;

class ChargeRankController extends StatisticController
{
    public function index(Request $request)
    {
        $begin = $this->getBeginTime($request);
        $end = $this->getEndTime($request);

        $data = ChargeRankModel::getRank($begin, $end);

        $header = collect([
            (new Header())->field('role_id')->title(trans('admin.role.id'))->align(),
            (new Header())->field('role_name')->title(trans('admin.user.name'))->align(),
            (new Header())->field('today_rank')->title(trans('admin.statistic.rank.today'))->align(),
            (new Header())->field('today')->title(trans('admin.statistic.number'))->align(),
            (new Header())->field('total_rank')->title(trans('admin.statistic.rank.total'))->align(),
            (new Header())->field('total')->title(trans('admin.statistic.number.total'))->align(),
        ]);

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
