<?php

namespace App\Admin\Models\ActiveStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRegisterModel extends DistributionModel
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

    public static function getDayAvg(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("register_time", [$begin, $end])
            ->groupBy(["date"])
            ->select([
                DB::raw("COUNT(1) AS `number`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%H') AS `date`"),
            ])
            ->get();
    }

    public static function getAvg(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("register_time", [$begin, $end])
            ->groupBy(["date"])
            ->select([
                DB::raw("COUNT(1) AS `number`"),
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`register_time`), '%Y-%m-%d') AS `date`"),
            ])
            ->get();
    }
}