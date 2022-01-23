<?php

namespace App\Admin\Models\GameConfigureModels;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Grid\Exporter;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

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
            if (!is_null($description) && is_bool(strpos($row["description"], $description))) {
                return false;
            }
            if (!is_null($file) && is_bool(strpos($row["file"], $file))) {
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
        if (is_int(strpos($path, "erl"))) {
            return self::erl();
        } else if (is_int(strpos($path, "lua"))) {
            return self::lua();
        } else if (is_int(strpos($path, "js"))) {
            return self::js();
        } else {
            throw new Exception("Unknown Path: $path");
        }
    }

    /**
     * Erl Data
     *
     * @return array
     * @throws Exception
     */
    static private function erl(): array
    {
        // read configure from data script
        $data = SwitchServerController::executeMakerScript(["data"]);
        return json_decode($data, true) ? : [];

    }

    /**
     * Lua Data
     *
     * @return array
     * @throws Exception
     */
    static private function lua(): array
    {
        // read configure from lua script
        $data = SwitchServerController::executeMakerScript(["lua"]);
        return json_decode($data, true) ? : [];
    }

    /**
     * Js Data
     *
     * @return array
     * @throws Exception
     */
    static private function js(): array
    {
        // read configure from js script
        $data = SwitchServerController::executeMakerScript(["js"]);
        return json_decode($data, true) ? : [];
    }
}
