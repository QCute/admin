<?php

namespace App\Admin\Models\GameConfigureModels;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ConfigureListModel extends Model
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
     * @throws Exception
     */
    public function paginate(): LengthAwarePaginator
    {
        $data = $this->getData();
        $total = count($data);
        // filter
        $description = request()->input("description");
        $file = request()->input("file");
        $data = array_filter($data, function ($row) use ($description, $file) {
            if (!is_null($description) && is_bool(strpos($row["description"], $description))) {
                return false;
            }
            if (!is_null($file) && is_bool(strpos($row["file"], $file))) {
                return false;
            }
            return true;
        });
        // slice
        $perPage = request()->input("per_page", env("ADMIN_PER_PAGE", 20));
        $page = request()->input("page", 1);
        $start = ($page - 1) * $perPage;
        $data = array_slice($data, $start, $perPage);
        // show
        $data = static::hydrate($data);
        $paginator = new LengthAwarePaginator($data, $total, $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
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
        return json_decode($data, true);
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
        return json_decode($data, true);
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
        return json_decode($data, true);
    }
}
