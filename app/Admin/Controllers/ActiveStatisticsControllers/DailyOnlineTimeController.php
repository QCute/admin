<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;
use App\Admin\Controllers\TimeTabController;

class DailyOnlineTimeController extends TimeTabController
{
    private static function slice($data)
    {
        $result = array(
            array("name" => "0-5" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "5-10" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "10-20" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "20-30" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "30-40" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "40-50" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "50-60" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "60-70" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "70-80" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "80-90" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "90-100" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
            array("name" => "100-∞" . trans("admin.minute"), "value" => 0, "label" => array("formatter" => "{b}: {c} ({d}%)")),
        );
        foreach ($data as $row)
        {
            if ($row->time < (5 * 60))
            {
                $result[0]["value"]++;
            }
            else if ($row->time < (11 * 60))
            {
                $result[1]["value"]++;
            }
            else if ($row->time < (21 * 60))
            {
                $result[2]["value"]++;
            }
            else if ($row->time < (31 * 60))
            {
                $result[3]["value"]++;
            }
            else if ($row->time < (41 * 60))
            {
                $result[4]["value"]++;
            }
            else if ($row->time < (51 * 60))
            {
                $result[5]["value"]++;
            }
            else if ($row->time < (61 * 60))
            {
                $result[6]["value"]++;
            }
            else if ($row->time < (71 * 60))
            {
                $result[7]["value"]++;
            }
            else if ($row->time < (81 * 60))
            {
                $result[8]["value"]++;
            }
            else if ($row->time < (91 * 60))
            {
                $result[9]["value"]++;
            }
            else if ($row->time < (101 * 60))
            {
                $result[10]["value"]++;
            }
            else
            {
                $result[11]["value"]++;
            }
        }
        return $result;
    }

    public function index(Content $content)
    {
        list($before, $now, $current, $nav) = $this->makeNav(array("week", "month", "all", "pick_time"), "week");
        $data = DB::select("SELECT `role_id`, SUM( `online_time` ) AS `time`, DATE_FORMAT( FROM_UNIXTIME(`login_log`.`logout_time`), '%Y-%m-%d' ) AS `date` FROM " . SwitchServerController::getCurrentServer(). ".`login_log` WHERE `time` BETWEEN " . $before . " AND " . $now . " GROUP BY `date`, `role_id` ORDER BY `logout_time` ASC ");
        // chart data
        if (empty($data))
        {
            $color = "color: ['rgba(128, 128, 128, 1)']";
            $data = array(array("name" => "0", "value" => "0", "label" => array("formatter" => "{b}: {c} (100%)")));
        }
        else
        {
            $data = self::slice($data);
            $color = "color: ['#37a2da','#32c5e9','#9fe6b8','#ffdb5c','#ff9f7f','#fb7293','#e7bcf3','#8378ea', '#5bc2e7', '#6980c5', '#12ED93', '#f376e0']";
        }
        // draw
        return $content->body("
            {$nav}
            <div id='chart' style='width: 100%; height: 100%; position: relative;'></div>
            <script>
            $(function () {
                echarts.init(document.getElementById('chart'), 'shine').setOption({
                    grid: { left: '0px', right: '0px', top: '100px', bottom: '200px', containLabel: true },
                    legend: {type: 'scroll', orient: 'vertical', right: 50, top: 50, bottom: 50},
                    series: [{type: 'pie', radius: '60%', center: ['50%', '45%'], {$color}, data: " . json_encode($data) . "}]
                });
            });
            </script>
        ");
    }
}
