<?php

namespace App\Admin\Models\ChargeStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChargeDistributionModel extends DistributionModel 
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

    public static function getRate(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("time", [$begin, $end])
            ->groupBy(["name"])
            ->select([
                DB::raw("`money` AS `name`"),
                DB::raw("COUNT(1) AS `value`"),
            ])
            ->get();
    }
}
