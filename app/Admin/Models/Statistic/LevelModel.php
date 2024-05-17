<?php

namespace App\Admin\Models\Statistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LevelModel extends DistributionModel
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

    public static function getRank(int $limit = 100): Collection
    {
        return (new static())
            ->groupBy("level")
            ->orderBy("level", "DESC")
            ->limit($limit)
            ->select([
                "level",
                DB::raw("COUNT(`role_id`) AS `number`"),
            ])
            ->get();
    }
}