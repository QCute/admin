<?php

namespace App\Admin\Models\GameConfigure;

use App\Admin\Models\Model;
use App\Admin\Models\Extend\ServerModel;
use App\Admin\Services\Extend\DatabaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConfigureTableModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'admin';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configure_table';

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())
            ->withInput($input)
            ->with('log')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function findByNameOrComment(string $nameOrComment): ConfigureTableModel|null
    {
        return (new static())
            ->with('log')
            ->where('table_name', '=', $nameOrComment)
            ->orWhere('table_comment', '=', $nameOrComment)
            ->first();
    }

    public function log()
    {
        return $this->hasMany(ImportLogModel::class, 'table_name', 'table_name')->latest();
    }

    /**
     * Reload data
     *
     * @param ServerModel $server
     */
    public static function reload(ServerModel $server)
    {
        // import log
        $logs = ImportLogModel::getLastLog($server->db_name)->toArray();

        // data
        $data = DatabaseService::changeConnection($server)
            ->table('information_schema.TABLES')
            ->where('TABLE_SCHEMA', '=', $server->db_name)
            ->where('TABLE_NAME', 'LIKE', '%_data')
            ->get([
                'TABLE_SCHEMA',
                'TABLE_NAME',
                'TABLE_COMMENT', 
            ])
            ->toArray();

        // fill log message
        foreach ($data as &$row) {

            // fill log
            $log = $logs[$row->TABLE_COMMENT] ?? (object)['operator' => '', 'name' => '', 'state' => ''];
            $row->operator = $log->operator;
            $row->name = $log->name;
            $row->state = $log->state;
            $row = (array)$row;

        }

        // id
        // TABLE_SCHEMA
        // TABLE_NAME
        // TABLE_COMMENT

        // operator
        // name
        // state

        // created_time
        // updated_time
        // deleted_time

        (new static())->upsert($data, ['TABLE_SCHEMA', 'TABLE_NAME', 'TABLE_COMMENT']);
    }
}
