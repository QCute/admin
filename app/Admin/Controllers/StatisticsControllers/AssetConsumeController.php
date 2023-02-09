<?php

namespace App\Admin\Controllers\StatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class AssetConsumeController extends ChartController
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content): Content
    {
        // check
        $server = SwitchServerController::getCurrentServer();
        if (empty($server)) {
            return $content
                ->title($this->title())
                ->withWarning("Could not found current server");
        }

        // view
        list($before, $now, $active) = $this->getTime("day");

        $assetData = SwitchServerController::getDB()
            ->table("asset_data")
            ->select([
                DB::raw("`asset` AS `name`"),
                DB::raw("`name` AS `title`"),
                DB::raw("'asset' AS `key`"),
                DB::raw("`asset` AS `value`"),
            ])
            ->get()
            ->toArray();
        // asset tab
        $asset = request()->input("asset", $assetData[0]-> name);

        $builder = SwitchServerController::getDB()
            ->table("asset_consume_log")
            ->where("asset", $asset)
            ->whereBetween("time", [$before, $now])
            ->groupBy("from")
            ->select([
                DB::raw("`from` AS `name`"),
                DB::raw("SUM(`number`) AS `number`")
            ]);
        // role filter
        $role_id = request()->input("role_id", "");
        // with role filter
        if(!empty($role_id)) {
            $builder->where("role_id", $role_id);
        }
        $data = $builder
            ->get()
            ->toArray();
        // chart data
        if (empty($data)) {
            $color = ['rgba(128, 128, 128, 1)'];
            $data = [["name" => "0", "value" => "0", "label" => ["formatter" => "{b}: {c} (100%)"]]];
        } else {
            $data = array_map(function ($row) { return ["name" => $row->name, "value" => $row->number, "label" => ["formatter" => "{b}: {c} ({d}%)"]]; }, $data);
            $color = ['#37a2da', '#32c5e9', '#9fe6b8', '#ffdb5c', '#ff9f7f', '#fb7293', '#e7bcf3', '#8378ea', '#5bc2e7', '#6980c5', '#12ED93', '#f376e0'];
        }
        // grid
        $grid = [
            'left' => '0px',
            'right' => '0px',
            'top' => '25px',
            'bottom' => '0px',
            'containLabel' => true
        ];
        // legend
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
        $option = [
            'grid' => $grid,
            'legend' => $legend,
            'series' => $series,
        ];
        $chart = $this->makeChart($option, $active, 164);
        // search
        $search = $this->makeSearch("role_id", $role_id);
        // asset tab
        $tab = $this->makeTab($assetData, $asset, "$search$chart");
        // time tab
        $tab = $this->makeTimeTab(["day", "week", "month", "all", "pick"], $active, $tab);
        // draw
        return $content->title("")->body($tab);
    }
}
