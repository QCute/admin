<?php

namespace App\Admin\Controllers\ChargeStatistic;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Controllers\Extend\StatisticController;
use App\Admin\Models\ChargeStatistic\ChargeDailyModel;
use Illuminate\Http\Request;

class ChargeDailyController extends StatisticController
{
    public function index(Request $request)
    {
        $begin = $this->getBeginTime($request);
        $end = $this->getEndTime($request);

        $data = ChargeDailyModel::getDaily($begin, $end);

        $header = collect([
            (new Header())->field('date')->title(trans('admin.time.date'))->align(),

            (new Header())->field('register')->title(trans('admin.statistic.register'))->align(),
            (new Header())->field('login')->title(trans('admin.statistic.login'))->align(),

            (new Header())->field('new_user')->title(trans('admin.statistic.new.user'))->align(),
            (new Header())->field('new_times')->title(trans('admin.statistic.new.times'))->align(),
            (new Header())->field('new_money')->title(trans('admin.statistic.new.money'))->align(),

            (new Header())->field('user')->title(trans('admin.statistic.user'))->align(),
            (new Header())->field('times')->title(trans('admin.statistic.times'))->align(),
            (new Header())->field('money')->title(trans('admin.statistic.money'))->align(),

            (new Header())->field('arp_u')->title('ARP-U')->align(),
            (new Header())->field('arp_pu')->title('ARP-PU')->align(),
            (new Header())->field('rate')->title(trans('admin.statistic.rate'))->align(),
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