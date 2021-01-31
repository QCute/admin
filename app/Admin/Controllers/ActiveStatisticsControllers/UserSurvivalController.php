<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Admin\Controllers\TimeTabController;
use App\Admin\Controllers\SwitchServerController;

class UserSurvivalController extends TimeTabController
{
    private static function statistics($time, $register)
    {
        $survival = [0, 0, 0, 0, 0, 0, 0, 0];
        $charge = [0, 0, 0, 0, 0, 0, 0, 0];
        $free = [0, 0, 0, 0, 0, 0, 0, 0];
        foreach ($register as $row)
        {
            $offset = ($row->date - $time) / 3600;
            if ($offset <= 2 && $offset <= 3)
            {
                $survival[0] += $row->survival;
                $charge[0] += $row->charge;
                $free[0] += $row->free;
            }
            if ($offset <= 3 && $offset <= 4)
            {
                $survival[1] += $row->survival;
                $charge[1] += $row->charge;
                $free[1] += $row->free;
            }
            if ($offset <= 4 && $offset <= 5)
            {
                $survival[2] += $row->survival;
                $charge[2] += $row->charge;
                $free[2] += $row->free;
            }
            if ($offset <= 5 && $offset <= 6)
            {
                $survival[3] += $row->survival;
                $charge[3] += $row->charge;
                $free[3] += $row->free;
            }
            if ($offset <= 6 && $offset <= 7)
            {
                $survival[4] += $row->survival;
                $charge[4] += $row->charge;
                $free[4] += $row->free;
            }
            if ($offset <= 7 && $offset <= 8)
            {
                $survival[5] += $row->survival;
                $charge[5] += $row->charge;
                $free[5] += $row->free;
            }
            if ($offset <= 7 && $offset <= 15)
            {
                $survival[6] += $row->survival;
                $charge[6] += $row->charge;
                $free[6] += $row->free;
            }

            $survival[7] += $row->survival;
            $charge[7] += $row->charge;
            $free[7] += $row->free;
        }
        return ["survival" => $survival, "charge" => $charge, "free" => $free];
    }

    public function index(Content $content)
    {
        list(, , , $nav) = $this->makeNav(["all"], "all");
        // 1.次日存活=（次日登陆总数-次日注册总数）/ 首日注册总数
        // 2.3日存活=（第三日登陆总数-第三日注册总数）/ 首日+第二日注册总数
        // 3.周存活率=（开服第8天登录-开服第8天注册）/ 开服第1周总注册
        // 4.15日存活率=（开服第15天登陆-开服第15天注册）/ 开服15日总注册
        // 5.30日存活率=（开服第30天登陆-开服第30天注册）/ 开服30日总注册
        $database = SwitchServerController::getCurrentServer();
        $before = SwitchServerController::getCurrentServerOpenTime();
        $now = SwitchServerController::getCurrentServerOpenTime() + (30 * 86400);
        $data = DB::select("SELECT COUNT(1) AS `number`, COUNT(`first_recharge_time` != 0) AS `charge`, COUNT(`first_recharge_time` = 0) AS `free`, DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%Y-%m-%d') AS `date` FROM `{$database}`.`role` WHERE `register_time` BETWEEN ? AND ? GROUP BY `date` ORDER BY `date` ASC", [$before, $now]);
        // server open time offset
        $before -= 86400;
        // $time_list = implode(",", [$before + (2 * 86400), $before + (3 * 86400), $before + (4 * 86400), $before + (5 * 86400), $before + (6 * 86400), $before + (7 * 86400), $before + (15 * 86400), $before + (30 * 86400)]);
        $login = DB::select("SELECT COUNT(1) AS `total`, CONCAT(\"'\", DATE_FORMAT(FROM_UNIXTIME(`time`), '%m-%d'), \"'\") AS `date` FROM `{$database}`.`login_log` WHERE `time` BETWEEN ? AND ? GROUP BY `date` ORDER BY `date` ASC", [$before, $now]);
        // chart data
        if (empty($data))
        {
            $category = implode(",", array_map(function ($row) { return "'" . $row . trans("admin.time-day") . "'"; }, ["次", 3, 4, 5, 6, 7, 15, 30]));
            $login = implode(",", [0, 0, 0, 0, 0, 0, 0, 0]);
            $survival = implode(",", [0, 0, 0, 0, 0, 0, 0, 0]);
            $charge = implode(",", [0, 0, 0, 0, 0, 0, 0, 0]);
            $free = implode(",", [0, 0, 0, 0, 0, 0, 0, 0]);
        }
        else
        {
            // foreach ($login as $row) { $login[$row->date] = $row->total; }
            $data = self::statistics($now, $data);
            $category = implode(",", array_map(function ($row) { return "'" . $row . trans("admin.time-day") . "'"; }, ["次", 3, 4, 5, 6, 7, 15, 30]));
            $login = implode(",", array_column($login, "total"));
            $survival = implode(",", $data["survival"]);
            $charge = implode(",", $data["charge"]);
            $free = implode(",", $data["free"]);
        }
        // nav
        // $list = implode("", array_map(function($time) { if($time == $this->getTime()) return "<li role='presentation' class='active' ><a>" . trans("admin." . $time) . "</a></li>"; else return "<li role='presentation' ><a href='user-survival?time={$time}'>" . trans("admin." . $time) . "</a></li>"; }, ["day", "week", "month", "all"]));
        // draw
        return $content->title('')->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '20px', bottom: '100px', containLabel: true },
                    legend: { icon: 'circle', top: '5%', right: '0%', itemWidth: 6, itemGap: 20, textStyle: { color: '#556677' }},
                    xAxis: { type: 'category', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } }, data: [{$category}] },
                    yAxis: { type: 'value', splitLine: { show: false }, axisTick: { show: false }, axisLabel: { textStyle: { color: '#556677' } }, axisLine: { lineStyle: { color: '#DCE2E8' } } },
                    series: [
                        {name: '" . trans("admin.login") . "', type: 'bar', smooth: true, itemStyle: { normal: { color: '#00B400', borderColor: '#28B416', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$login}] },
                        {name: '" . trans("admin.survival") . "', type: 'bar', smooth: true, itemStyle: { normal: { color: '#F56948', borderColor: '#F56948', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$survival}] },
                        {name: '" . trans("admin.charge") . "', type: 'bar', smooth: true, itemStyle: { normal: { color: '#73DDFF', borderColor: '#73DDFF', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$charge}] },
                        {name: '" . trans("admin.free") . "', type: 'bar', smooth: true, itemStyle: { normal: { color: '#9E87FF', borderColor: '#9E87FF', lineStyle: { width: 5, shadowColor: 'rgba(158,135,255, 0.3)', shadowBlur: 10, shadowOffsetY: 20 } } }, data: [{$free}] },
                    ]
                });
            });
            </script>
        ");
    }
}
