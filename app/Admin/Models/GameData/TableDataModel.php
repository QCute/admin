<?php

namespace App\Admin\Models\GameData;

use App\Admin\Models\Extend\DistributionModel;
use App\Admin\Traits\FormInput;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

class TableDataModel extends DistributionModel 
{
    use FormInput;

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
    protected $table = '';

    public static function getPage(string $table, int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        $model = (new static());
        $model->setTable($table);
        return $model
            ->withInput($input)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function getSimplePage(string $table, int $page, int $perPage, array $input = []): Paginator
    {
        $model = (new static());
        $model->setTable($table);
        return $model
            ->withInput($input)
            ->simplePaginate($perPage, ['*'], 'page', $page);
    }
}
