<?php

namespace App\Admin\Models\ActiveStatistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserOnlineModel extends DistributionModel
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
    protected $table = 'online_log';

    public static function getDayAvg(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("time", [$begin, $end])
            ->groupBy(["date"])
            ->select([
                DB::raw("AVG(`total`) AS `total`"),
                DB::raw("AVG(`online`) AS `online`"),
                DB::raw("AVG(`hosting`) AS `hosting`"),
                DB::raw("`hour` AS `date`"),
            ])
            ->get();
    }

    public static function getAvg(int $begin, int $end): Collection
    {
        return (new static())
            ->whereBetween("time", [$begin, $end])
            ->groupBy(["date"])
            ->select([
                "total",
                "online",
                "hosting",
                DB::raw("DATE_FORMAT(FROM_UNIXTIME(`time`), '%H:%i') AS `date`"),
            ])
            ->get();
    }
}