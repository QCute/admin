<?php

namespace App\Admin\Controllers\ChargeStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\DB;

class ChargeRankController extends ChartController
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
        list($before, $now, $active) = $this->getTime("pick");
        $data = SwitchServerController::getDB()
            ->table("charge")
            ->leftJoin("role", "role.role_id", "charge.role_id")
            ->whereBetween("charge.time", [$before, $now])
            ->groupBy("charge.role_id")
            ->orderBy("today_rank", "ASC")
            ->select([
                DB::raw("charge.`role_id`"),
                DB::raw("role.`role_name`"),
                DB::raw("ROW_NUMBER() OVER ( ORDER BY `today` DESC) AS `today_rank`"),
                DB::raw("SUM(charge.`money`) AS `today`"),
                DB::raw("ROW_NUMBER() OVER ( ORDER BY `total` DESC) AS `total_rank`"),
                DB::raw("role.`charge_total` AS `total`"),
            ])
            ->get()
            ->toArray();

        // table
        $headers = [
            trans('admin.role_id'),
            trans('admin.role_name'),
            trans('admin.today') . " " . trans('admin.rank'),
            trans('admin.charge'),
            trans('admin.total') . " " . trans('admin.rank'),
            trans('admin.charge'),
        ];

        $table = new Table($headers, $data, ['table-hover']);
        // box
        $tab = $this->makeTimeTab(["pick"], $active, $table->render());
        // draw
        return $content->title("")->body($tab);
    }
}
