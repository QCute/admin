<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
            case "all_server":
                $start = request()->input("start-date", date('Y-m-d', time()));
                $end = request()->input("end-date", date('Y-m-d', time()));
                return [strtotime($start), strtotime($end), $active];
            default:
                throw new \Exception("Unhandled type: $active", 1);
        }
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
    public function makeChart(array $option = [], string $active = '', int $padding = 164, int $height = 0): string
    {
        $id = Str::random();
        $option = json_encode($option);
        // add time picker height
        $padding = $active == "pick" || $active == "all_server" ? $padding + 34 + 10 : $padding;
        $height = $height == 0 ? "calc(100vh - {$padding}px)" : "{$height}px";
        return "
            <div id='chart-{$id}' style='width: 100%; height: $height; position: relative;'></div>
            <script>
                $(function () {
                    const chart = echarts.init(document.getElementById('chart-{$id}'), 'shine');
                    chart.setOption($option);
                    window.onresize = () => chart.resize();
                    (new ResizeObserver(() => chart.resize())).observe(document.querySelector('#chart-{$id}'));
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
    public function makeTimeTab(array $array, string $active, string $content): string
    {
        $url = request()->url();
        $start_date = request()->input("start-date", date_format(date_create(),"Y-m-d"));
        $end_date = request()->input("end-date", date_format(date_create("+1 day"),"Y-m-d"));
        $tomorrow = date_format(date_create("+1 day"),"Y-m-d");
        $open_time = date('Y-m-d', SwitchServerController::getCurrentServerOpenTime());
        // date picker
        if ($active == "pick" || $active == "all_server") {
            $datePicker = "
                <div style='display: flex;margin-bottom: 10px;padding: unset;'>
                    <div class='input-group'>
                        <span class='input-group-addon input-group-addon-front' style='cursor: pointer;'><i class='fa fa-calendar'></i></span>
                        <input class='form-control date' type='text' name='start-date' value='{$start_date}'>
                    </div>
                    <div class='input-group'>
                        <span class='input-group-addon input-group-addon-back' style='cursor: pointer;'><i class='fa fa-arrows-h'></i></span>
                        <input class='form-control date' type='text' name='end-date' value='{$end_date}'>
                    </div>
                    <span class='input-group'>
                        <a class='btn btn-info' style='border-radius: unset;' onclick=\"this.href += '&start-date=' + document.querySelector('[name=start-date]').value + '&end-date=' + document.querySelector('[name=end-date]').value\" href='$url?time=$active'>
                            " . trans("admin.confirm") . "
                        </a>
                    </span>
                </div>
                <script>
                    $(function () { 
                        $('.date').datetimepicker({ 'format': 'YYYY-MM-DD', defaultDate: 'now', 'locale' : moment.locale('" . config("app.locale") . "'), 'allowInputToggle' : true});
                        document.querySelector('.input-group-addon-front').onclick = () => {
                            document.querySelector('[name=start-date]').value = '$open_time';
                        };
                        document.querySelector('.input-group-addon-back').onclick = () => {
                            document.querySelector('[name=end-date]').value = '$tomorrow';
                        };
                    });
                </script>
            ";
        } else {
            $datePicker = "";
        }
        // tab
        $array = array_map(function ($row) {
            return ["name" => $row, "title" => trans("admin." . $row), "key" => "time", "value" => $row];
        }, $array);
        return $this->makeTab($array, $active, "$datePicker$content");
    }
    /**
     * Make Search.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public function makeSearch(string $key = "", string $value = ""): string
    {
        $url = request()->url();
        $query = Arr::query(request()->query());
        // search
        return "
            <div style='display: flex;margin-bottom: 10px;padding: unset;'>
                <div class='input-group'>
                    <span class='input-group-addon input-group-addon-front' style='cursor: pointer;'><i class='fa fa-user'></i></span>
                    <input class='form-control' type='text' name='$key' value='$value' placeholder='" . trans("admin.$key") . "'>
                </div>
                <span class='input-group'>
                    <a class='btn btn-info' style='border-radius: unset;' onclick=\"this.href += '$key=' + document.querySelector('[name=$key]').value\" href='$url?$query&'>
                        " . trans("admin.filter") . "
                    </a>
                </span>
            </div>
        ";
    }

    public function makeTab(array $array = [], string $active = "", string $content = ""): string
    {
        // the tab
        $tab = new Tab();
        // the url
        $url = request()->url();
        // the request
        $input = request()->query();
        // build request
        foreach ($array as $row) {
            $row = (array)$row;
            $input[$row["key"]] = $row["value"];
            $query = Arr::query($input);
            if ($row["name"] == $active) {
                $tab->add($row["title"], $content, true);
            } else {
                $tab->addLink($row["title"], "$url?$query", false);
            }
        }
        // view
        return $tab->render();
    }
}
