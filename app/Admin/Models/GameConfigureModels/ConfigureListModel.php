<?php

namespace App\Admin\Models\GameConfigureModels;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class ConfigureListModel extends Model
{
    use DefaultDatetimeFormat;
    protected $table = '';
    protected $data = '';
    public function __construct($data = '')
    {
        $this->data = $data;
        parent::__construct();
    }

    /** Make page
     *
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function paginate(): LengthAwarePaginator
    {
        switch ($this->data) {
            case "erl": $data = self::erl(); break;
            case "lua": $data = self::lua(); break;
            case "js": $data = self::js(); break;
            default: throw new Exception("Unknown Route {$this->data}");
        }
        // filter
        $description = request()->input("description");
        $file = request()->input("file");
        $data = array_filter($data, function ($row) use ($description, $file) {
            if (!is_null($description) && is_bool(strpos($row["description"], $description))) {
                return false;
            }
            if (!is_null($file) &&is_bool(strpos($row["file"], $file))) {
                return false;
            }
            return true;
        });
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $start = ($page - 1) * $perPage;
        $data = static::hydrate(array_slice($data, $start, $perPage));
        $paginator = new LengthAwarePaginator($data, count($data), $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }

    static private function collectData($description, $file): array
    {
        return [
            "description" => $description,
            "file" => $file,
            "operation" => "",
        ];
    }

    static private function erl(): array
    {
        // read configure from data script
        $script = file_get_contents(env("SERVER_PATH") . "/script/make/script/data_script.erl");
        $script = substr($script, strpos($script, "data() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.erl/", $script, $file);
        return array_map("self::collectData", $name[0], $file[0]);
    }

    static private function lua(): array
    {
        // read configure from data script
        $script = file_get_contents(env("SERVER_PATH") . "/script/make/script/lua_script.erl");
        $script = substr($script, strpos($script, "lua() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.lua/", $script, $file);
        return array_map("self::collectData", $name[0], $file[0]);
    }

    static private function js(): array
    {
        // read configure from data script
        $script = file_get_contents(env("SERVER_PATH") . "/script/make/script/js_script.erl");
        $script = substr($script, strpos($script, "js() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.js/", $script, $file);
        return array_map("self::collectData", $name[0], $file[0]);
    }
}
