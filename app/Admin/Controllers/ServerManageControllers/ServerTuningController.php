<?php

namespace App\Admin\Controllers\ServerManageControllers;

use App\Admin\Controllers\SwitchServerController;
use App\Admin\Forms\ServerManageForms\ServerTuningForm;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServerTuningController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     * @throws Exception
     */
    public function index(Content $content): Content
    {
        // check
        $server = SwitchServerController::getCurrentServerNode();
        if (empty($server)) {
            return $content
                ->title($this->title())
                ->withWarning("Could not found current server");
        }
        // view
        $action = request()->input("action", "");
        if (!empty($action)) {
            return $this->action($content, $action);
        }
        return $this->displayIndex($content);
    }

    /**
     * Index.
     *
     * @param Content $content
     * @return Content
     * @throws Exception
     */
    public function displayIndex(Content $content): Content
    {
        $server = SwitchServerController::getCurrentServerNode();
        $running = SwitchServerController::executeRunScript([$server, "state"]);
        $owner = $this->getServerLockOwner($server);
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body(new ServerTuningForm(json_decode($running), $owner));
    }

    /**
     * Action interface.
     *
     * @return string
     * @throws Exception
     */
    public function getServerTime() : string
    {
        $datetime = SwitchServerController::execute(["date", "+%s"]);
        return date("Y-m-d H:i:s", intval($datetime));
    }

    /**
     * Action interface.
     *
     * @return string
     * @throws Exception
     */
    public function getServerState() : string
    {
        $server = SwitchServerController::getCurrentServerNode();
        $running = SwitchServerController::executeRunScript([$server, "state"]);
        return json_decode($running) ? trans("admin.server_active") : trans("admin.server_down");
    }

    /**
     * Get server lock owner
     *
     * @param string $server
     * @return string
     */
    public function getServerLockOwner(string $server = ""): string
    {
        $server = empty($server) ? SwitchServerController::getCurrentServerNode() : $server;
        $file = storage_path("logs/server_state.json");
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            return $data[$server]["owner"] ?? "";
        } else {
            return "";
        }
    }

    /**
     * Set server lock owner
     *
     * @param string $owner
     * @param string $server
     */
    public function setServerLockOwner(string $owner = "", string $server = "")
    {
        $server = empty($server) ? SwitchServerController::getCurrentServerNode() : $server;
        $file = storage_path("logs/server_state.json");
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
        } else {
            $data = [];
        }
        $data[] = [$server => ["owner" => $owner, "time" => now()]];
        $data = json_encode($data);
        file_put_contents($file, $data);
    }

    /**
     * Action interface.
     *
     * @param Content $content
     * @param string $action
     * @return Content
     * @throws Exception
     */
    public function action(Content $content, string $action): Content
    {
        switch ($action) {
            case "set-server-time": {
                $dateTime = request()->input("time");
                // super user necessary
                SwitchServerController::executeMakerScript(["date", "-s", $dateTime]);
                return $this->displayIndex($content)->withSuccess(trans("admin.succeeded"));
            }
            case "set-server-open-time": {
                $server = SwitchServerController::getCurrentServerNode();
                $openTime = request()->input("time");
                SwitchServerController::executeMakerScript(["cfg", "set", $server, "main, open_time", strtotime($openTime)]);
                return $this->displayIndex($content)->withSuccess(trans("admin.succeeded"));
            }
            case "server-start": {
                $server = SwitchServerController::getCurrentServerNode();
                SwitchServerController::executeMakerScript([$server, "start"]);
                return $this->displayIndex($content)->withSuccess(trans("admin.succeeded"));
            }
            case "server-stop": {
                $server = SwitchServerController::getCurrentServerNode();
                SwitchServerController::executeMakerScript([$server, "stop"]);
                return $this->displayIndex($content)->withSuccess(trans("admin.succeeded"));
            }
            case "server-truncate": {
                $db = SwitchServerController::getDB();
                $data = $db
                    ->table("information_schema.TABLES")
                    ->select("TABLE_NAME")
                    ->where("TABLE_SCHEMA", DB::raw("DATABASE()"))
                    ->where("TABLE_NAME", "NOT LIKE", "%_data")
                    ->get()
                    ->toArray();
                foreach ($data as $row) {
                    $db->table($row->TABLE_NAME)->truncate();
                }
                return $this->displayIndex($content)->withSuccess(trans("admin.succeeded"));
            }
            case "server-lock": {
                $server = SwitchServerController::getCurrentServerNode();
                $name = $this->getServerLockOwner();
                if(empty($name)) {
                    $this->setServerLockOwner(Auth::user()->username, $server);
                    return $this->displayIndex($content->withSuccess(trans("admin.succeeded")));
                } else if ($name == Auth::user()->username) {
                    $this->setServerLockOwner("", $server);
                    return $this->displayIndex($content->withSuccess(trans("admin.succeeded")));
                } else {
                    return $this->displayIndex($content->withError("not owner"));
                }
            }
            default: return $this->displayIndex($content)->withError("Unknown action: $action");
        }
    }

}
