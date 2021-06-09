<?php

namespace App\Admin\Models\GameConfigureModels;

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

    public function paginate()
    {
        switch ($this->data) {
            case "erl": $data = self::erl(); break;
            case "lua": $data = self::lua(); break;
            case "js": $data = self::js(); break;
            default: throw new \Exception("Unknown Route {$this->data}");
        }
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $start = ($page - 1) * $perPage;
        $data = static::hydrate(array_slice($data, $start, $perPage));
        $paginator = new LengthAwarePaginator($data, count($data), $perPage);
        $paginator->setPath(url()->current());
        return $paginator;
    }

    static private function collectData($description, $file)
    {
        return [
            "description" => $description,
            "file" => $file,
            "operation" => "",
        ];
    }

    static private function erl()
    {
        // read configure from data script
        $script = file_get_contents(env("SERVER_PATH") . "/script/make/script/data_script.erl");
        $script = substr($script, strpos($script, "data() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.erl/", $script, $file);
        return array_map("self::collectData", $name[0], $file[0]);
    }

    static private function lua()
    {
        // read configure from data script
        $script = file_get_contents(env("SERVER_PATH") . "/script/make/script/lua_script.erl");
        $script = substr($script, strpos($script, "lua() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.lua/", $script, $file);
        return array_map("self::collectData", $name[0], $file[0]);
    }

    static private function js()
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
