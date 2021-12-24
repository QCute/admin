<?php

namespace App\Admin\Models\GameConfigureModels;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Grid\Exporter;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ConfigureTableModel extends Model
{
    use DefaultDatetimeFormat;

    protected $table = '';

    /**
     * Make page
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        $data = $this->getData();
        $total = count($data);
        // slice
        $perPage = request()->input("per_page", env("ADMIN_PER_PAGE", 20));
        $page = request()->input("page", 1);
        $data = array_splice($data, ($page - 1) * $perPage, $perPage);
        // show
        $data = static::hydrate($data);
        $paginator = new LengthAwarePaginator($data, $total, $perPage, $page);
        $paginator->setPath(url()->current());
        return $paginator;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this
     */
    public function where(string $column, string $operator = null, string $value = null, string $boolean = 'and'): ConfigureTableModel
    {
        return $this;
    }

    /**
     * Export chunk
     *
     * @param int $count
     * @param callable $callback
     * @return bool
     */
    public function chunk(int $count, callable $callback): bool
    {
        $data = $this->getData();
        $mode = request()->input(Exporter::$queryName, "all");
        if (str_contains($mode, "page")) {
            // slice
            $perPage = request()->input("per_page", env("ADMIN_PER_PAGE", 20));
            $page = request()->input("page", 1);
            $data = array_slice($data, ($page - 1) * $perPage, $perPage);
        }
        // export
        $data = static::hydrate($data);
        call_user_func($callback, $data);
        return true;
    }

    /**
     * Get Data
     *
     * @return array
     */
    private function getData(): array
    {
        // log
        $server = SwitchServerController::getCurrentServer();
        $sub = DB::table("table_import_log")
            ->select(Db::raw("MAX(id)"))
            ->where("table_schema", $server)
            ->groupBy("table_name");
        $log = DB::table("table_import_log")
            ->select(["user_name", "table_name", "table_comment", "time", "state"])
            ->whereRaw(DB::raw("id in ({$sub->toSql()})"), $sub->getBindings())
            ->get()
            ->toArray();
        $map = array_reduce($log, function ($acc, $row) {
            $acc[$row->table_comment] = $row;
            return $acc;
        }, []);
        // filter
        $table_comment = request()->input("TABLE_COMMENT");
        $table_comment = is_null($table_comment) ? "%" : "%$table_comment%";
        $table_name = request()->input("TABLE_NAME");
        $table_name = is_null($table_name) ? "%" : "%$table_name%";
        // data
        $data = SwitchServerController::getDB()
            ->table("information_schema.TABLES")
            ->where("TABLE_SCHEMA", "=", SwitchServerController::getCurrentServer())
            ->where("TABLE_NAME", "LIKE", "%_data")
            ->where("TABLE_COMMENT", "LIKE", $table_comment)
            ->where("TABLE_NAME", "LIKE", $table_name)
            ->get(["TABLE_COMMENT", "TABLE_NAME"])
            ->toArray();
        // fill log message
        foreach ($data as $row) {
            if (!array_key_exists($row->TABLE_COMMENT, $map)) continue;
            $join = $map[$row->TABLE_COMMENT];
            $row->user_name = $join->user_name;
            $row->time = $join->time;
            $row->state = $join->state;
        }
        return $data;
    }
}
