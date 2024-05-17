<?php

namespace App\Admin\Models\ActiveStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DailyOnlineTimeModel extends DistributionModel
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
    protected $table = 'login_log';

    public static function getDaily(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("time", [$begin, $end])
            ->groupBy(["date", "role_id"])
            ->orderBy("logout_time")
            ->select([
                "role_id",
                DB::raw("SUM( `online_time` ) AS `time`"),
                DB::raw("DATE_FORMAT( FROM_UNIXTIME(`logout_time`), '%Y-%m-%d' ) AS `date`"),
            ])
            ->get();
    }
}