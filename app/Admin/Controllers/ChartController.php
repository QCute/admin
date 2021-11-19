<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

class ChartController extends Controller
{
    /**
     * Get Time.
     *
     * @param string $default
     * @return array
     */
    private function getTime(string $default = "day"): array
    {
        $current = request()->input("time", $default);
        switch ($current) {
            case "hour":
                return [time() - (60 * 60), time(), $current];
            case "day":
                return [strtotime(date('Y-m-d', time())), time(), $current];
            case "week":
                return [strtotime("-1 monday"), time(), $current];
            case "month":
                return [strtotime(date('Y-m-01', time())), time(), $current];
            case "all":
                $start = SwitchServerController::getCurrentServerOpenTime();
                return [$start, time(), $current];
            case "pick":
                $start = request()->input("start-date", date('Y-m-d', time()));
                $end = request()->input("end-date", date('Y-m-d', time()));
                return [strtotime($start), strtotime($end), $current];
        }
        return [];
    }

    /**
     * Make Tab.
     *
     * @param array $array
     * @param string $default
     * @return array
     */
    public function makeTab(array $array, string $default = "day"): array
    {
        $url = request()->url();
        list($before, $now, $current) = $this->getTime($default);
        // make tab list
        $list = implode("", array_map(function ($time) use ($url, $current) {
            if ($time == $current)
                return "<li role='presentation' class='active' ><a>" . trans("admin." . $time) . "</a></li>";
            else
                return "<li role='presentation' ><a href='$url?time=$time'>" . trans("admin." . $time) . "</a></li>";
        }, $array));
        // the tab
        $tab = "
            <style>.nav-tabs > li > a { border-radius: unset; } </style>
            <style>.date-picker-group{ float: right; " . ($current == "pick" ? "" : "display: none;") . "}</style>
            <div class='col-sm-1 date-picker-group'>
                <div class='input-group'>
                    <a id='picker-ok' onclick=\"this.href += '&start-date=' + $('#start-date').val() + '&end-date=' + $('#end-date').val()\" href='$url?time=pick'>
                        <input type='submit' class='form-control btn-primary' value='" . trans("admin.confirm") . "' />
                    </a>
                </div>
            </div> 
            <div class='col-sm-3 date-picker-group'>
                <div class='input-group'>
                    <div class='input-group date date-picker'>
                        <span class='input-group-addon'>" . trans("admin.end") . " " . trans("admin.time") . "：<span class='glyphicon glyphicon-calendar'></span></span>
                        <input type='text'  id='end-date' class='form-control' />
                    </div>
                </div>
            </div>
            <div class='col-sm-3 date-picker-group'>
                <div class='input-group date date-picker' >
                    <span class='input-group-addon'>" . trans("admin.start") . " " . trans("admin.time") . "：<span class='glyphicon glyphicon-calendar'></span></span>
                    <input type='text' id='start-date' class='form-control' />
                </div>
            </div>
            <ul class='nav nav-tabs' style=''>$list</ul>
            <script type='text/javascript'>
                $(function () { $('.date-picker').datetimepicker({ format: 'YYYY-MM-DD', defaultDate: 'now', locale: moment.locale('" . config("locale") . "') }); });
            </script>
        ";
        // view
        return [$before, $now, $current, $tab];
    }

    /**
     * Make Chart.
     *
     * @param array $grid
     * @param array $legend
     * @param array $xAxis
     * @param array $yAxis
     * @param array $series
     * @return string
     */
    public function makeChart(array $grid = [], array $legend = [], array $xAxis = [], array $yAxis = [], array $series = []): string
    {
        if (empty($grid)) {
            $grid = json_encode([
                'left' => '0px',
                'right' => '0px',
                'top' => '25px',
                'bottom' => '0px',
                'containLabel' => true
            ]);
        }
        $legend = json_encode($legend);
        $xAxis = json_encode($xAxis);
        $yAxis = json_encode($yAxis);
        $series = json_encode($series);
        return "
            <div id='chart' style='width: 100%; height: calc(100vh - 165px); position: relative;'></div>
            <script>
            $(function () {
                let chart = echarts.init(document.getElementById('chart'), 'shine');
                chart.setOption({
                    grid: $grid,
                    legend: $legend,
                    xAxis: $xAxis,
                    yAxis: $yAxis,
                    series: $series
                });
                window.onresize = () => chart.resize();
            });
            </script>
        ";
    }
}
