<?php

namespace App\Admin\Models\ChargeStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FirstChargeTimeDistributionModel extends DistributionModel 
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
    protected $table = 'role';

    public static function getRate(): Collection
    {
        return (new static())
            ->where("first_charge_time", ">", "0")
            ->join('charge', 'role.role_id', '=', 'charge.role_id')
            ->groupBy(["time"])
            ->select([
                DB::raw("(`charge`.`first_charge_time` - `role`.`register_time`) div 86400 + 1 AS `time`"),
                DB::raw("COUNT(1) AS `value`"),
            ])
            ->get();
    }
}
