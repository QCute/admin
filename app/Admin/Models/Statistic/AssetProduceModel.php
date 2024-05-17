<?php

namespace App\Admin\Models\Statistic;

use App\Admin\Models\Extend\DistributionModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssetProduceModel extends DistributionModel
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
    protected $table = 'asset_produce_log';

    public static function getRate(int $begin, int $end, string|null $asset = null): Collection
    {
        $model = new static();
        $assetData = $model
            ->getConnection()
            ->table("asset_data")
            ->get();

        // asset type
        $asset = $asset ?? $assetData[0]->asset;

        return $model
            ->where("asset", $asset)
            ->whereBetween("time", [$begin, $end])
            ->groupBy("from")
            ->select([
                DB::raw("`from` AS `name`"),
                DB::raw("SUM(`number`) AS `number`")
            ])
            ->get();
    }
}