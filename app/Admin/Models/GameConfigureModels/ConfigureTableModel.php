<?php

namespace App\Admin\Models\GameConfigureModels;

use App\Admin\Controllers\SwitchServerController;
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

    /** Make page
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $start = ($page - 1) * $perPage;
        // filter
        $comment = request()->input("TABLE_COMMENT");
        $comment = is_null($comment) ? "%" : "%{$comment}%";
        $name = request()->input("TABLE_NAME");
        $name = is_null($name) ? "%" : "%{$name}%";
        // log
        $log = DB::table("table_import_log")
            ->select("name")
            ->distinct()
            ->select(["name", "comment", "username", "time"])
            ->get()
            ->toArray();
        $map = [];
        foreach ($log as $row) {
            $map[$row->comment] = $row;
        }
        // data
        $data = DB::connection(SwitchServerController::getConnection())
            ->table("information_schema.TABLES")
            ->where("TABLE_SCHEMA", "=", $this->database)
            ->where("TABLE_NAME", "LIKE", "%_data")
            ->where("TABLE_COMMENT", "LIKE", $comment)
            ->where("TABLE_NAME", "LIKE", $name)
            ->offset($start)
            ->limit($perPage)
            ->get(["TABLE_COMMENT", "TABLE_NAME"])
            ->toArray();
        foreach ($data as $row) {
            if (!array_key_exists($row->TABLE_COMMENT, $map)) continue;
            $join = $map[$row->TABLE_COMMENT];
            $row->username = $join->username;
            $row->time = $join->time;
        }
        $data = static::hydrate($data);
        $paginator = new LengthAwarePaginator($data, count($data), $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }
}
