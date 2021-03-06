<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;

class DailyOnlineTimeController extends ChartController
{
    private static function slice($data): array
    {
        $result = [
            ["name" => "0-5" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "5-10" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "10-20" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "20-30" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "30-40" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "40-50" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "50-60" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "60-70" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "70-80" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "80-90" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "90-100" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
            ["name" => "100-∞" . trans("admin.minute"), "value" => 0, "label" => ["formatter" => "{b}: {c} ({d}%)"]],
        ];
        foreach ($data as $row) {
            if ($row->time < (5 * 60)) {
                $result[0]["value"]++;
            } else if ($row->time < (11 * 60)) {
                $result[1]["value"]++;
            } else if ($row->time < (21 * 60)) {
                $result[2]["value"]++;
            } else if ($row->time < (31 * 60)) {
                $result[3]["value"]++;
            } else if ($row->time < (41 * 60)) {
                $result[4]["value"]++;
            } else if ($row->time < (51 * 60)) {
                $result[5]["value"]++;
            } else if ($row->time < (61 * 60)) {
                $result[6]["value"]++;
            } else if ($row->time < (71 * 60)) {
                $result[7]["value"]++;
            } else if ($row->time < (81 * 60)) {
                $result[8]["value"]++;
            } else if ($row->time < (91 * 60)) {
                $result[9]["value"]++;
            } else if ($row->time < (101 * 60)) {
                $result[10]["value"]++;
            } else {
                $result[11]["value"]++;
            }
        }
        return $result;
    }

    public function index(Content $content): Content
    {
        list($before, $now, , $tab) = $this->makeTab(["week", "month", "all", "pick"], "week");
        $data = SwitchServerController::getDB()
            ->table("login_log")
            ->whereBetween("time", [$before, $now])
            ->groupBy(["date", "role_id"])
            ->orderBy("logout_time")
            ->select([
                "role_id",
                DB::raw("SUM( `online_time` ) AS `time`"),
                DB::raw("DATE_FORMAT( FROM_UNIXTIME(`logout_time`), '%Y-%m-%d' ) AS `date`"),
            ])
            ->get()
            ->toArray();
        // chart data
        if (empty($data)) {
            $color = ['rgba(128, 128, 128, 1)'];
            $data = [["name" => "0", "value" => "0", "label" => ["formatter" => "{b}: {c} (100%)"]]];
        } else {
            $data = self::slice($data);
            $color = ['#37a2da', '#32c5e9', '#9fe6b8', '#ffdb5c', '#ff9f7f', '#fb7293', '#e7bcf3', '#8378ea', '#5bc2e7', '#6980c5', '#12ED93', '#f376e0'];
        }
        // chart
        $legend = [
            'type' => 'scroll',
            'orient' => 'vertical',
            'right' => 50,
            'top' => 50,
            'bottom' => 50
        ];
        $series = [
            'type' => 'pie',
            'radius' => '60%',
            'center' => ['50%', '45%'],
            'color' => $color,
            'data' => $data,
        ];
        $chart = $this->makeChart([], $legend, [], [], $series);
        // draw
        return $content->title("")->body(new Box("", "{$tab}{$chart}"));
    }
}
