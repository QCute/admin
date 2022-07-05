<?php

namespace App\Admin\Models\GameConfigureModels;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Grid\Exporter;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ConfigureListModel extends Model
{
    use DefaultDatetimeFormat;

    protected $table = '';

    /** Make page
     *
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function paginate(): LengthAwarePaginator
    {
        $data = $this->getData();
        $total = count($data);
        // filter
        $data = $this->filter($data);
        // slice
        $perPage = request()->input("per_page", env("ADMIN_PER_PAGE", 20));
        $page = request()->input("page", 1);
        $data = array_slice($data, ($page - 1) * $perPage, $perPage);
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
    public function where(string $column, string $operator = null, string $value = null, string $boolean = 'and'): ConfigureListModel
    {
        $this->query[] = ["column" => $column, "operator" => $operator, "value" => $value, "boolean" => $boolean];
        return $this;
    }

    /**
     * Export chunk
     *
     * @param int $count
     * @param callable $callback
     * @return bool
     * @throws Exception
     */
    public function chunk(int $count, callable $callback): bool
    {
        $data = $this->getData();
        $data = $this->filter($data);
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
     * Collect data
     *
     * @param array $data
     * @return array
     */
    private function filter(array $data = []): array
    {
        $description = request()->input("description");
        $file = request()->input("file");
        return array_filter($data, function ($row) use ($description, $file) {
            if (!is_null($description) && is_bool(strpos($row->description, $description))) {
                return false;
            }
            if (!is_null($file) && is_bool(strpos($row->file, $file))) {
                return false;
            }
            return true;
        });
    }

    /**
     * Get Data
     *
     * @return array
     * @throws Exception
     */
    private function getData(): array
    {
        $path = request()->path();
        $current = SwitchServerController::getCurrentServer();
        $server = SwitchServerController::getServer($current);
        // list
        if (is_int(strpos($path, "erl"))) {
            $data = self::erl();
            $status = self::repository_status();
        } else if (is_int(strpos($path, "lua"))) {
            $data = self::lua();
            $status = self::repository_status($server->configure_root);
        } else if (is_int(strpos($path, "js"))) {
            $data = self::js();
            $status = self::repository_status($server->configure_root);
        } else {
            throw new Exception("Unknown Path: $path");
        }
        // import log
        $sub = DB::table("table_import_log")
            ->select(Db::raw("MAX(id)"))
            ->where("table_schema", $current)
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
        // fill log message
        foreach ($data as $row) {
            if (array_key_exists($row->description, $map)) {
                $join = $map[$row->description];
                $row->user_name = $join->user_name;
                $row->time = $join->time;
                $row->action = $join->state;
                $row->state = $join->state == "0" ? '<i class="fa fa-check" style="color: green"></i>' : '<i class="fa fa-lock" style="color: red"></i>';
            }
            $basename = basename($row->file);
            if (array_key_exists($basename, $status)) {
                $row->status = $status[$basename];
            }
        }
        return $data;
    }

    /**
     * Execute get repository status
     *
     * @param string|null $path
     * @param object|null $config
     * @param string $output
     * @return array
     * @throws Exception
     */
    private static function repository_status(string $path = null, object $config = null, string $output = "stdout"): array
    {
        $status = "";
        try {
            $status .= SwitchServerController::execute(["git", "status", "--porcelain"], $path, $config, $output);
        } catch (Exception) {
            // suppress error when dir not a git repository
        }
        try {
            $status .= SwitchServerController::execute(["svn", "status"], $path, $config, $output);
        } catch (Exception) {
            // suppress error when dir not a svn repository
        }
        $data = explode("\n", $status);
        $array = [];
        foreach ($data as $row) {
            $row = preg_replace("/\s+/", ",", trim($row));
            if (empty($row)) continue;
            [$mode, $file] = explode(",", $row);
            switch ($mode) {
                case "A":
                    $array[basename($file)] = trans("admin.add");
                    break;
                case "D":
                    $array[basename($file)] = trans("admin.delete");
                    break;
                case "M":
                    $array[basename($file)] = trans("admin.modify");
                    break;
                default:
                    $array[basename($file)] = trans("admin.unknown");
                    break;
            }
        }
        return $array;
    }

    /**
     * Erl Data
     *
     * @return array
     * @throws Exception
     */
    private static function erl(): array
    {
        // read configure from data script
        $data = SwitchServerController::executeMakerScript(["data"]);
        return json_decode($data) ? : [];
    }

    /**
     * Lua Data
     *
     * @return array
     * @throws Exception
     */
    private static function lua(): array
    {
        // read configure from lua script
        $data = SwitchServerController::executeMakerScript(["lua"]);
        return json_decode($data) ? : [];
    }

    /**
     * Js Data
     *
     * @return array
     * @throws Exception
     */
    private static function js(): array
    {
        // read configure from js script
        $data = SwitchServerController::executeMakerScript(["js"]);
        return json_decode($data) ? : [];
    }
}
