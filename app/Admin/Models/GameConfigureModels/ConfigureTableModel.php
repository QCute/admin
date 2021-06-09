<?php

namespace App\Admin\Models\GameConfigureModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class ConfigureTableModel extends Model
{
    use DefaultDatetimeFormat;
    protected $database = '';
    protected $table = 'table_import_log';
    public function __construct($database)
    {
        $this->database = $database;
        parent::__construct();
    }

    public function paginate()
    {
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $start = ($page - 1) * $perPage;
        // data
        $sql = "SELECT COUNT(1) AS `number` FROM information_schema.`TABLES` LEFT JOIN (SELECT `table_import_log`.`username`, `table_import_log`.`comment`, `table_import_log`.`time` FROM `table_import_log` JOIN (SELECT MAX(`id`) AS `id` FROM `table_import_log` GROUP BY `comment`) AS `group_table_import_log` ON `table_import_log`.`id` = `group_table_import_log`.`id`) AS `table_import_log` ON `TABLES`.`TABLE_COMMENT` = `table_import_log`.`comment` WHERE `TABLE_SCHEMA` = '{$this->database}' AND `TABLE_NAME` LIKE '%_data'";
        $total = DB::selectOne($sql)->number;
        $sql = "SELECT `TABLES`.`TABLE_COMMENT`, `TABLES`.`TABLE_NAME`, `table_import_log`.* FROM information_schema.`TABLES` LEFT JOIN (SELECT `table_import_log`.`username`, `table_import_log`.`comment`, `table_import_log`.`time` FROM `table_import_log` JOIN (SELECT MAX(`id`) AS `id` FROM `table_import_log` GROUP BY `comment`) AS `group_table_import_log` ON `table_import_log`.`id` = `group_table_import_log`.`id`) AS `table_import_log` ON `TABLES`.`TABLE_COMMENT` = `table_import_log`.`comment` WHERE `TABLE_SCHEMA` = '{$this->database}' AND `TABLE_NAME` LIKE '%_data' LIMIT {$start}, $perPage";
        $data = static::hydrate(DB::select($sql));
        $paginator = new LengthAwarePaginator($data, $total, $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }

}
