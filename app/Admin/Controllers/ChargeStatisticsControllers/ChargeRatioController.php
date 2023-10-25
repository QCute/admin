<?php

namespace App\Admin\Controllers\ChargeStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;

class ChargeRatioController extends ChartController
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
        $server = SwitchServerController::getCurrentServerNode();
        if (empty($server)) {
            return $content
                ->title($this->title())
                ->withWarning("Could not found current server");
        }
        // view
        list($before, $now, $active) = $this->getTime("all");
        $total = SwitchServerController::getDB()
            ->table("role")
            ->whereBetween("register_time", [$before, $now])
            ->select([
                DB::raw("COUNT(*) AS `total`"),
            ])
            ->get()
            ->toArray();
        $charge = SwitchServerController::getDB()
            ->table("role")
            ->whereBetween("register_time", [$before, $now])
            ->where("first_charge_time", ">", "0")
            ->select([
                DB::raw("COUNT(*) AS `charge`"),
            ])
            ->get()
            ->toArray();
        if ($charge[0]->charge == 0) {
            $color = ['rgba(128, 128, 128, 1)'];
            $data = [['name' => '0', 'value' => '0', 'label' => ['formatter' => '{b}: {c} (100%)']]];
        }
        else {
            $color = ['#37a2da','#32c5e9','#9fe6b8','#ffdb5c','#ff9f7f','#fb7293','#e7bcf3','#8378ea'];
            $data = [
                [
                    'name' => trans('admin.charge'),
                    'value' => $charge[0]->charge,
                    'label' => ['formatter' => '{b}: {c} ({d}%)']
                ], [
                    'name' => trans('admin.free'),
                    'value' => $total[0]->total - $charge[0]->charge,
                    'label' => ['formatter' => '{b}: {c} ({d}%)']
                ]
            ];
        }
        // chart
        $grid = [
            'left' => '0px',
            'right' => '0px',
            'top' => '25px',
            'bottom' => '0px',
            'containLabel' => true
        ];
        $legend = [
            'type' => 'scroll',
            'orient' => 'vertical',
            'top' => '50',
            'right' => '50',
            'bottom' => '50'
        ];
        $series = [
            'type' => 'pie',
            'radius' => '60%',
            'center' => ['50%', '45%'],
            'color' => $color,
            'data' => $data
        ];
        $option = [
            'grid' => $grid,
            'legend' => $legend,
            'series' => $series,
        ];
        $chart = $this->makeChart($option, $active);
        $tab = $this->makeTimeTab(["all", "pick"], $active, $chart);
        // draw
        return $content->title("")->body($tab);
    }
}
