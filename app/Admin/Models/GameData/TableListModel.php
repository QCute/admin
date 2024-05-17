<?php

namespace App\Admin\Models\GameData;

use App\Admin\Models\Extend\ServerModel;
use App\Admin\Models\Extend\DistributionModel;
use App\Admin\Traits\FormInput;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

class TableListModel extends DistributionModel
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
    protected $table = 'information_schema.TABLES';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public static function getPage(ServerModel $server, string $type, int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        $builder = (new static())->withInput($input)->where('TABLE_SCHEMA', '=', $server->db_name);

        switch($type) {
            case 'user': $builder->where('TABLE_NAME', 'NOT LIKE', '%_data')->where('TABLE_NAME', 'NOT LIKE', '%_log');break;
            case 'configure': $builder->where('TABLE_NAME', 'LIKE', '%_data');break;
            case 'log': $builder->where('TABLE_NAME', 'LIKE', '%_log');break;
        }

        return $builder->paginate($perPage, ['TABLE_NAME AS id', 'TABLE_SCHEMA', 'TABLE_NAME', 'TABLE_COMMENT', 'TABLE_ROWS'], 'page', $page);
    }

    public static function getSimplePage(ServerModel $server, string $type, int $page, int $perPage, array $input = []): Paginator
    {
        $builder = (new static())->withInput($input)->where('TABLE_SCHEMA', '=', $server->db_name);

        switch($type) {
            case 'user': $builder->where('TABLE_NAME', 'NOT LIKE', '%_data')->where('TABLE_NAME', 'NOT LIKE', '%_log');break;
            case 'configure': $builder->where('TABLE_NAME', 'LIKE', '%_data');break;
            case 'log': $builder->where('TABLE_NAME', 'LIKE', '%_log');break;
        }

        return $builder->simplePaginate($perPage, ['TABLE_NAME AS id', 'TABLE_SCHEMA', 'TABLE_NAME', 'TABLE_COMMENT', 'TABLE_ROWS'], 'page', $page);
    }
}
