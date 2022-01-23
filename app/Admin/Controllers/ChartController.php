<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Widgets\Tab;

class ChartController extends AdminController
{
    /**
     * Get Time.
     *
     * @param string $default
     * @return array
     */
    public function getTime(string $default = "day"): array
    {
        $active = request()->input("time", $default);
        switch ($active) {
            case "hour":
                return [time() - (60 * 60), time(), $active];
            case "day":
                return [strtotime(date('Y-m-d', time())), time(), $active];
            case "week":
                return [strtotime("-1 monday"), time(), $active];
            case "month":
                return [strtotime(date('Y-m-01', time())), time(), $active];
            case "all":
                $start = SwitchServerController::getCurrentServerOpenTime();
                return [$start, time(), $active];
            case "pick":
                $start = request()->input("start-date", date('Y-m-d', time()));
                $end = request()->input("end-date", date('Y-m-d', time()));
                return [strtotime($start), strtotime($end), $active];
        }
        return [];
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

    /**
     * Make Tab.
     *
     * @param array $array
     * @param string $active
     * @param string $chart
     * @return string
     */
    public function makeTab(array $array, string $active, string $chart): string
    {
        $url = request()->url();
        // date picker
        if ($active == "pick") {
            $datePicker = "
                <div name='date-picker' class='col-lg-12 col-md-12 col-xs-12 col-sm-12' style='width:300px; float: left; margin-top: -50px;'>
                    <div class='col-lg-6 col-md-6 col-xs-6 col-sm-6 center'>
                        <div class='input-group'>
                            <span class='input-group-addon'><i class='fa fa-calendar'></i></span>
                            <input type='text' name='start-date' class='form-control date' style='width: 100px'>
                        </div>
                    </div>
                    <div class='col-lg-6 col-md-6 col-xs-6 col-sm-6 center'>
                        <div class='input-group'>
                            <span class='input-group-addon'><i class='fa fa-arrows-h'></i></span>
                            <input type='text' name='end-date' class='form-control date' style='width: 100px'>
                            <span class='input-group-btn'>
                                <a id='picker-ok' onclick=\"this.href += '&start-date=' + document.querySelector('[name=start-date]').value + '&end-date=' + document.querySelector('[name=end-date]').value\" href='$url?time=pick'>
                                    <button class='form-control btn-info' type='button'>" . trans("admin.confirm") . "</button>
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <script type='text/javascript'>
                    $(function () { 
                        $('.date').datetimepicker({ 'format': 'YYYY-MM-DD', defaultDate: 'now', 'locale' : moment.locale('" . config("app.locale") . "'), 'allowInputToggle' : true});
                        const width = Array.from(document.querySelector('.nav-tabs').children).reduce((a, e) => a + e.clientWidth, -30);
                        document.querySelector('[name=date-picker]').style.marginLeft = width + 'px';
                    });
                </script>
            ";
        } else {
            $datePicker = "";
        }
        // tab
        $tab = new Tab();
        foreach ($array as $time) {
            if ($time == $active) {
                $tab->add(trans("admin." . $time), "$datePicker$chart", "$url?time=$time", true);
            } else {
                $tab->addLink(trans("admin." . $time), "$url?time=$time", false);
            }
        }
        // view
        return $tab->render();
    }
}
