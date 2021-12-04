<?php

namespace App\Admin\Models\GameConfigureModels;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ConfigureTableModel extends Model
{
    use DefaultDatetimeFormat;
    protected $table = '';
    public function __construct()
    {
        parent::__construct();
    }

    /** Make page
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        $perPage = request()->input("per_page", env("ADMIN_PER_PAGE", 20));
        $page = request()->input("page", 1);
        $start = ($page - 1) * $perPage;
        // filter
        $comment = request()->input("TABLE_COMMENT");
        $comment = is_null($comment) ? "%" : "%$comment%";
        $name = request()->input("TABLE_NAME");
        $name = is_null($name) ? "%" : "%$name%";
        // log
        $log = DB::table("table_import_log")
            ->select("name")
            ->distinct()
            ->select(["name", "comment", "username", "time"])
            ->get()
            ->toArray();
        $map = array_reduce($log, function ($acc, $row) { $acc[$row->comment] = $row; return $acc; }, []);
        // data
        $data = SwitchServerController::getDB()
            ->table("information_schema.TABLES")
            ->where("TABLE_SCHEMA", "=", SwitchServerController::getCurrentServer())
            ->where("TABLE_NAME", "LIKE", "%_data")
            ->where("TABLE_COMMENT", "LIKE", $comment)
            ->where("TABLE_NAME", "LIKE", $name)
            ->get(["TABLE_COMMENT", "TABLE_NAME"])
            ->toArray();
        $total = count($data);
        // slice
        $data = array_splice($data, $start, $perPage);
        // fill log message
        foreach ($data as $row) {
            if (!array_key_exists($row->TABLE_COMMENT, $map)) continue;
            $join = $map[$row->TABLE_COMMENT];
            $row->username = $join->username;
            $row->time = $join->time;
        }
        // show
        $data = static::hydrate($data);
        $paginator = new LengthAwarePaginator($data, $total, $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }
}
