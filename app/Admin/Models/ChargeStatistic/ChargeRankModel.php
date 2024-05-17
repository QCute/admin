<?php

namespace App\Admin\Models\ChargeStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChargeRankModel extends DistributionModel 
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

    public static function getRank(int $begin, int $end): Collection
    {
        return (new static())
            ->leftJoin("role", "role.role_id", "charge_order.role_id")
            ->leftJoin("charge", "charge.role_id", "charge_order.role_id")
            ->whereBetween("charge_order.time", [$begin, $end])
            ->groupBy("charge_order.role_id")
            ->orderBy("today_rank", "ASC")
            ->select([
                DB::raw("charge_order.`role_id`"),
                DB::raw("role.`role_name`"),
                DB::raw("ROW_NUMBER() OVER ( ORDER BY `today` DESC) AS `today_rank`"),
                DB::raw("SUM(charge_order.`money`) AS `today`"),
                DB::raw("ROW_NUMBER() OVER ( ORDER BY `total` DESC) AS `total_rank`"),
                DB::raw("charge.`charge_total` AS `total`"),
            ])
            ->get();
    }
}
