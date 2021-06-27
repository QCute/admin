<?php

namespace App\Admin\Models\GameConfigureModels;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Symfony\Component\Process\Process;

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

    /**
     * @throws Exception
     */
    static private function erl(): array
    {
        // read configure from data script
        $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "data"], null, ["PATH" => `echo \$PATH`]);
        $process->run();
        // result
        if (!$process->isSuccessful() || !empty($process->getErrorOutput())) {
            throw new Exception($process->getErrorOutput());
        }
        return json_decode($process->getOutput());
    }

    /**
     * @throws Exception
     */
    static private function lua(): array
    {
        // read configure from lua script
        $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "lua"], null, ["PATH" => `echo \$PATH`]);
        $process->run();
        // result
        if (!$process->isSuccessful() || !empty($process->getErrorOutput())) {
            throw new Exception($process->getErrorOutput());
        }
        return json_decode($process->getOutput());
    }

    /**
     * @throws Exception
     */
    static private function js(): array
    {
        // read configure from js script
        $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "js"], null, ["PATH" => `echo \$PATH`]);
        $process->run();
        // result
        if (!$process->isSuccessful() || !empty($process->getErrorOutput())) {
            throw new Exception($process->getErrorOutput());
        }
        return json_decode($process->getOutput());
    }
}
