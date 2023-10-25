<?php

namespace App\Admin\Controllers\ActiveStatisticsControllers;

use App\Admin\Controllers\ChartController;
use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class UserSurvivalController extends ChartController
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
        for($date = $before; $date < $now; $date += 86400) {
            
            // role number
            $sub = SwitchServerController::getDB()
                ->table("role")
                ->whereBetween("role.register_time", [$date, $date + 86400])
                ->select([
                    DB::raw("'" . date('Y-m-d', $date) . "' AS `date`"),
                    DB::raw("COUNT(role.`role_id`) AS `number`"),
                ]);
            
            // row base
            $row = SwitchServerController::getDB()->table($sub);

            for($day = 0; $day < 30; $day++) {
                if($day > 5 && $day != 14 && $day != 29) continue;
                // each day
                $sub = SwitchServerController::getDB()
                    ->table("role")
                    ->whereBetween("role.register_time", [$date, $date + 86400]) // date
                    ->leftJoin('login_log', function (JoinClause $join) use ($date, $day) {
                        $join
                            ->on('role.role_id', '=', 'login_log.role_id')
                            ->whereBetween('login_log.time', [$date + ($day + 1) * 86400, $date + ($day + 2) * 86400]); // the date offset day
                    })
                    ->select([
                        DB::raw("CONCAT(COUNT(DISTINCT login_log.`role_id`), '/', COUNT(DISTINCT role.`role_id`), '(', FORMAT(IFNULL(COUNT(DISTINCT login_log.`role_id`) * 100 / COUNT(DISTINCT role.`role_id`), 0), 2), '%', ')') AS `day_$day`"),
                    ]);
                // row sub
                $row->joinSub($sub, "role_$day", function() {});
            }

            // union all row
            $table = $table ? $table->unionAll($row) : $row;
        }

        $data = $table ? array_reverse($table->get()->toArray()) : [];


        // table
        $headers = [
            trans("admin.date"),
            trans("admin.total"),
            2 . trans("admin.day"),
            3 . trans("admin.day"),
            4 . trans("admin.day"),
            5 . trans("admin.day"),
            6 . trans("admin.day"),
            7 . trans("admin.day"),
            14 . trans("admin.day"),
            30 . trans("admin.day"),
        ];

        $rows = array_values($data);
        $table = new Table($headers, $rows, ['table-hover']);
        // box
        $tab = $this->makeTimeTab(["pick"], $active, $table->render());
        // draw
        return $content->title("")->body($tab);
    }
}
