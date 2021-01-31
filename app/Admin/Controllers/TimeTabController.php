<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

class TimeTabController extends Controller
{
    private function getTime($default = "day")
    {
        $current = request()->input("time", $default);
        switch ($current)
        {
            case "hour": {
                return [time() - (60 * 60), time(), $current];
            }
            case "day": {
                return [strtotime(date('Y-m-d', time())), time(), $current];
            }
            case "week": {
                return [strtotime("-1 monday"), time(), $current];
            }
            case "month": {
                return [strtotime(date('Y-m-01', time())), time(), $current];
            }
            case "all": {
                $start = SwitchServerController::getCurrentServerOpenTime();
                return [$start, time(), $current];
            }
            case "pick_time": {
                $start = request()->input("start-date", date('Y-m-d', time()));
                $end = request()->input("end-date", date('Y-m-d', time()));
                return [strtotime($start), strtotime($end), $current];
            }
        }
        return [];
    }

    public function makeNav($array, $default = "day")
    {
        $url = request()->path();
        list($before, $now, $current) = $this->getTime($default);
        // make nav list
        $list = implode("", array_map(function($time) use($url, $current) {
            if($time == $current)
                return "<li role='presentation' class='active' ><a>" . trans("admin." . $time) . "</a></li>";
            else
                return "<li role='presentation' ><a href='{$url}?time={$time}'>" . trans("admin." . $time) . "</a></li>";
        }, $array));

        // the nav
        $nav = "
        <style>#app, #pjax-container { height: 100%; overflow: hidden; } </style>
        <style>.content, .content > .row, .content > .row > .col-md-12 { height: 100%; background-color: white; } </style>
        <style>.nav-tabs > li > a { border-radius: unset; } </style>
        <style>.date-picker-group{ float: right; " . ($current == "pick_time" ? "" : "display: none;") . "}</style>
        <div class='col-sm-1 date-picker-group'>
            <div class='input-group'>
                <a id='picker-ok' onclick=\"this.href += '&start-date=' + $('#start-date').val() + '&end-date=' + $('#end-date').val()\" href='/{$url}?time=pick_time'><input type='submit' class='form-control' value='" . trans("admin.ok") . "' /></a>
            </div>
        </div> 
        <div class='col-sm-3 date-picker-group'>
            <div class='input-group'>
                <div class='input-group date date-picker'>
                    <span class='input-group-addon'>" . trans("admin.pick_time_end") . "：<span class='glyphicon glyphicon-calendar'></span></span>
                    <input type='text'  id='end-date' class='form-control' />
                </div>
            </div>
        </div>
        <div class='col-sm-3 date-picker-group'>
            <div class='input-group date date-picker' >
                <span class='input-group-addon'>" . trans("admin.pick_time_start") . "：<span class='glyphicon glyphicon-calendar'></span></span>
                <input type='text' id='start-date' class='form-control' />
            </div>
        </div>
        <ul class='nav nav-tabs' style=''>{$list}</ul>
        <script type='text/javascript'>
            $(function () { $('.date-picker').datetimepicker({ format: 'YYYY-MM-DD', defaultDate: 'now', locale: moment.locale('" . config("locale") . "') }); });
        </script>";
        // view
        return [$before, $now, $current, $nav];
    }
}
