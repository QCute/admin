<?php

namespace App\Admin\Models\GameConfigure;

use App\Admin\Models\Model;
use Illuminate\Database\Eloquent\Collection;

class ImportLogModel extends Model
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
    protected $table = 'import_log';

    public static function getLastLog(string $schema): Collection
    {
        $model = new static();

        $sub = $model
            ->selectRaw('MAX(`id`)')
            ->where('table_schema', $schema)
            ->groupBy('table_name');

        $log = $model
            ->whereRaw("`id` IN ({$sub->toSql()})", $sub->getBindings())
            ->get()
            ->keyBy('table_comment');

        return $log;
    }
}
