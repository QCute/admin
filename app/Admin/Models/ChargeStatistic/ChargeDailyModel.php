<?php

namespace App\Admin\Models\ChargeStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChargeDailyModel extends DistributionModel 
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'remote';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'charge_order';

    public static function getDaily(int $begin, int $end): Collection
    {
        $builder = null;
        $model = new static();
        for ($date = $begin; $date < $end; $date += 86400) {

            // role number
            $base = $model
                ->getConnection()
                ->table("role")
                ->whereBetween("role.register_time", [$date, $date + 86400])
                ->select([
                    DB::raw("'" . date("Y-m-d", $date) . "' AS `date`"),
                    DB::raw("COUNT(role.`role_id`) AS `register`"),
                ]);

            // row base
            $row = $model->getConnection()->table($base, 'base');

            // login
            $sub = $model
                ->getConnection()
                ->table("role")
                ->whereBetween("role.login_time", [$date, $date + 86400]) // date
                ->select([
                    DB::raw("COUNT(role.`role_id`) AS `login`"),
                ]);
            // login sub
            $row->joinSub($sub, "login", function () { });

            // new charge order
            $sub = $model
                ->leftJoin("role", "role.role_id", "=", "charge_order.role_id")
                ->whereBetween("charge_order.time", [$date, $date + 86400]) // date
                ->whereBetween("role.register_time", [$date, $date + 86400]) // date
                ->select([
                    DB::raw("COUNT(DISTINCT charge_order.`role_id`) AS `new_user`"),
                    DB::raw("COUNT(charge_order.`charge_no`) AS `new_times`"), 
                    DB::raw("IFNULL(SUM(charge_order.`money`), 0.00) AS `new_money`"),
                ]);

            // new charge order sub
            $row->joinSub($sub, "new", function () { });

            // total charge order
            $sub = $model
                ->whereBetween("charge_order.time", [$date, $date + 86400]) // date
                ->select([
                    DB::raw("COUNT(DISTINCT charge_order.`role_id`) AS `user`"),
                    DB::raw("COUNT(charge_order.`charge_no`) AS `times`"),
                    DB::raw("IFNULL(SUM(charge_order.`money`), 0.00) AS `money`"),
                ]);

            // total charge order sub
            $row->joinSub($sub, "total", function () { });

            $row->select([
                "*",
                DB::raw("ROUND(IFNULL(money / login, 0), 2) AS `arp_u`"),
                DB::raw("ROUND(IFNULL(money / user, 0), 2) AS `arp_pu`"),
                DB::raw("ROUND(IFNULL(user / login, 0), 2) AS `rate`"),
            ]);

            // union all row
            $builder = $builder ? $builder->unionAll($row) : $row;
        }

        return $builder ? $model->getConnection()->table($builder, 'outer')->groupBy('date')->get() : collect();
    }
}
