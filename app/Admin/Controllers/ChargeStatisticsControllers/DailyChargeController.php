<?php

namespace App\Admin\Controllers\ChargeStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class DailyChargeController extends ChartController
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
        list($before, $now, $active) = $this->getTime("pick");

        $table = null;
        for ($date = $before; $date < $now; $date += 86400) {

            // role number
            $sub = SwitchServerController::getDB()
                ->table("role")
                ->whereBetween("role.register_time", [$date, $date + 86400])
                ->select([
                    DB::raw("'" . date("Y-m-d", $date) . "' AS `date`"),
                    DB::raw("COUNT(role.`role_id`) AS `create`"),
                ]);

            // row base
            $row = SwitchServerController::getDB()->table($sub);

            // login
            $sub = SwitchServerController::getDB()
                ->table("role")
                ->whereBetween("role.login_time", [$date, $date + 86400]) // date
                ->select([
                    DB::raw("COUNT(role.`role_id`) AS `login`"),
                ]);
            // login sub
            $row->joinSub($sub, "login", function () { });

            // new charge
            $sub = SwitchServerController::getDB()
                ->table("charge")
                ->whereBetween("charge.time", [$date, $date + 86400]) // date
                ->leftJoin("role", function(JoinClause $join) use ($date) {
                    $join
                        ->on("role.role_id", "=", "charge.role_id")
                        ->whereBetween("role.register_time", [$date, $date + 86400]); // date
                })
                ->select([
                    DB::raw("COUNT(DISTINCT charge.`role_id`) AS `new_user`"),
                    DB::raw("COUNT(charge.`charge_no`) AS `new_times`"), 
                    DB::raw("IFNULL(SUM(charge.`money`), 0.00) AS `new_money`"),
                ]);
            // new charge sub
            $row->joinSub($sub, "new", function () { });

            // total charge
            $sub = SwitchServerController::getDB()
                ->table("charge")
                ->whereBetween("charge.time", [$date, $date + 86400]) // date
                ->select([
                    DB::raw("COUNT(DISTINCT charge.`role_id`) AS `user`"),
                    DB::raw("COUNT(charge.`charge_no`) AS `times`"),
                    DB::raw("IFNULL(SUM(charge.`money`), 0.00) AS `money`"),
                ]);
            // total charge sub
            $row->joinSub($sub, "total", function () { });

            $row->select([
                "*",
                DB::raw("IFNULL(money / login, 0) AS `arp_u`"),
                DB::raw("IFNULL(money / user, 0) AS `arp_pu`"),
                DB::raw("IFNULL(user / login, 0) AS `charge_rate`"),
            ]);
            // union all row
            $table = $table ? $table->unionAll($row) : $row;
        }

        $data = $table ? array_reverse($table->get()->toArray()) : [];

        $headers = [
            trans("admin.date"),

            trans("admin.create"),
            trans("admin.login"),

            trans("admin.new") . " " . trans("admin.charge") . " " . trans("admin.user"),
            trans("admin.new") . " " . trans("admin.charge") . " " . trans("admin.times"),
            trans("admin.new") . " " . trans("admin.charge"),

            trans("admin.charge") . " " . trans("admin.user"),
            trans("admin.charge") . " " . trans("admin.times"),
            trans("admin.charge"),

            "ARP-U",
            "ARP-PU",
            trans("admin.charge") . " " . trans("admin.ratio"),
        ];

        $rows = array_values($data);
        $table = new Table($headers, $rows, ["table-hover"]);

        $tab = $this->makeTimeTab(["pick"], $active, $table->render());
        // draw
        return $content->title("")->body($tab);
    }
}