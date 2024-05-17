<?php

namespace App\Admin\Models\ActiveStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserLoginModel extends DistributionModel
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

    public static function getDayAvg(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("login_time", [$begin, $end])
            ->groupBy(["date"])
            ->select([
                DB::raw("COUNT(DISTINCT `role_id`) AS `number`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`login_time`), '%H') AS `date`")
            ])
            ->get();
    }

    public static function getAvg(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("time", [$begin, $end])
            ->groupBy(["date"])
            ->select([
                DB::raw("COUNT(DISTINCT `role_id`) AS `number`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%Y-%m-%d') AS `date`")
            ])
            ->get();
    }
}