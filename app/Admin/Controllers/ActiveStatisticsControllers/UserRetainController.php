<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class UserRetainController extends Controller
{
    public function index(Content $content)
    {
        // 1.次日存活=（次日登陆总数-次日注册总数）/首日注册总数
        // 2.3日存活=（第三日登陆总数-第三日注册总数）/首日+第二日注册总数
        // 3.周存活率=（开服第8天登录-开服第8天注册）/开服第1周总注册
        // 4.15日存活率=（开服第15天登陆-开服第15天注册）/开服15日总注册
        // 5.30日存活率=（开服第30天登陆-开服第30天注册）/开服30日总注册
        $now = time();
        $before = strtotime(date("Y-m-d", $now - 86400 * intval(request()->input("time", 1)))) + 86400;
        $data = DB::select("SELECT COUNT(1) as `number`, DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%Y-%m-%d') as `date` FROM " . SwitchServerController::getCurrentServer() . ".`role` WHERE `register_time` BETWEEN " . $before . " AND " . $now . " GROUP BY `date`");
        $labels = array();
        $retain = array();
        foreach($data as $row) {
            $labels[] = $row->date;
            $retain[] = $row->number;
        }
        // json data
        $labels = implode(",", $labels);
        $retain = implode(",", $retain);
        // nav
        $list = implode("", array_map(function($time, $text) { if($time == request()->input("time", 1)) return "<li role='presentation' class='active' ><a>" . trans("admin." . $text) . "</a></li>"; else return "<li role='presentation' ><a href='user-retain?time={$time}'>" . trans("admin." . $text) . "</a></li>"; }, array(1, 7, 30, 10000), array("day", "week", "month", "all")));
        // draw
        return $content->body("
            <style>#pjax-container { overflow: hidden; } </style>
            <style>.content { background-color: white; } </style>
            <style>.nav-tabs > li > a { border-radius: unset; } </style>
            <ul class='nav nav-tabs'>
                {$list}
            </ul>
            <canvas id='online-chart'></canvas>
            <script>
            $(function () {
                new Chart(document.getElementById('online-chart').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: [{$labels}],
                        datasets: [{
                            label: '" . trans('admin.retain') . "',
                            data: [{$retain}],
                            backgroundColor: ['rgba(0, 0, 0, 0)'],
                            borderColor: ['rgba(224, 0, 0, 1)'],
                            borderWidth: 2
                        }]
                    }
                });
            });
            </script>
        ");
    }
}
