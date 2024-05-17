<?php

namespace App\Admin\Models\GameConfigure;

use App\Admin\Models\Model;
use App\Admin\Models\Extend\ServerModel;
use App\Admin\Services\Extend\MachineService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class ConfigureFileModel extends Model
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
    protected $table = 'configure_file';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tables' => AsCollection::class,
    ];

    public static function getPage(string $file, int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())
            ->withInput($input)
            ->with('log')
            ->where('file', 'LIKE', '%.' . $file)
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function findByFileOrComment(string $fileOrComment): ConfigureFileModel|null
    {
        return (new static())
            ->with('log')
            ->where('file', '=', $fileOrComment)
            ->orWhere('comment', '=', $fileOrComment)
            ->first();
    }

    public function log()
    {
        return $this->hasMany(ImportLogModel::class, 'file', 'file')->latest();
    }

    /**
     * Reload data
     *
     * @param ServerModel $server
     * @param string $type
     * @throws Exception
     */
    public static function reload(ServerModel $server, string $type)
    {
        // get data
        switch ($type) {
            case 'erl': {
                // read configure from erl script
                ["code" => $code, "msg" => $data] = MachineService::executeMakerScript($server, ['erl']);
                if($code > 0) {
                    throw new Exception($data, $code);
                }
                $data = json_decode($data) ? : [];
                $status = MachineService::repositoryStatus($server, $server->configure_root);
            };break;
            case 'lua': {
                // read configure from lua script
                ["code" => $code, "msg" => $data] = MachineService::executeMakerScript($server, ['lua']);
                if($code > 0) {
                    throw new Exception($data, $code);
                }
                $data = json_decode($data) ? : [];
                $status = MachineService::repositoryStatus($server, $server->configure_root);
            };break;
            case 'js': {
                // read configure from js script
                ["code" => $code, "msg" => $data] = MachineService::executeMakerScript($server, ['js']);
                if($code > 0) {
                    throw new Exception($data, $code);
                }
                $data = json_decode($data) ? : [];
                $status = MachineService::repositoryStatus($server, $server->configure_root);
            };break;
            default: {
                throw new Exception("Unknown Table: {$type}");
            }
        }

        // import log
        $logs = ImportLogModel::getLastLog($server->db_name)->toArray();

        // fill log message
        foreach ($data as &$row) {

            // fill log info
            $log = $logs[$row->comment] ?? (object)['operator' => '', 'name' => '', 'state' => ''];
            $row->operator = $log->operator;
            $row->name = $log->name;
            $row->state = $log->state;
            $row->tables = json_encode($row->tables);

            $basename = basename($row->file);

            // fill repository status
            $row->cvs = $status[$basename] ?? '';

            $row = (array)$row;
        }

        // id
        // file
        // comment

        // operator
        // name
        // state

        // cvs

        // created_time
        // updated_time
        // deleted_time

        (new static())->upsert($data, ['file', 'comment']);
    }
}
