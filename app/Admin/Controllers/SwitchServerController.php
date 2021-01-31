<?php

namespace App\Admin\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class SwitchServerController extends Controller
{
    public function index()
    {
        $server = request()->input("server");
        Cookie::queue("current_server", $server);
        if ($server)
            return response()->json(["result" => "ok", "server" => $server, "msg" => trans('admin.switch_server_ok')]);
        else
            return response()->json(["result" => "failed", "server" => $server, "msg" => trans('admin.switch_server_failed')]);
    }

    // nav bar server list
    public static function serverList()
    {
        $list = '';
        $data = self::getServerList("local");
        $currentServerNode = self::getCookieServer($data[0]->server_node);
        $currentServerName = $data[0]->server_name;
        foreach($data as $row) {
            if ($row->server_node === $currentServerNode) {
                $currentServerName = $row->server_name;
                continue;
            }
            // $list .= "<li><a class='server-node' onclick='switchServer(\"" . $row->server_node . "\")'>" . $row->server_name . "</a></li>";
            $list .= "<option value='{$row->server_node}'>" . $row->server_name . "</option>";
        }
        $list = "<option value='$currentServerNode'>" . $currentServerName . "</option>" . $list;
        return "
            <style>
                .server-select{margin: 8px 8px 0px 0px;}
                .x-server-list + .select2-container--default .select2-selection__rendered { line-height: 45px !important; }
                .x-server-list + .select2-container--default .select2-selection--single { height: 34px !important; }
            </style>
            <script>
            function switchServer(server) {
                $.ajax({
                    url: location.origin + '/' + 'switch-server?server=' + server,
                    success: () => $.pjax({container: '#pjax-container', url: location.pathname}),
                    error: (jqXHR, textStatus, errorThrown) => alert(errorThrown)
                });
            }
            </script>
            <li></li>
            <li class='server-select'><div class='input-group'><select class='form-control server-list' onchange='switchServer(this.value)' style='min-width:18em;outline:none;'>{$list}</select></div></li>
            
            <script>$(document).ready(function() { $('.server-list').select2({ placeholder: '" . trans("admin.chose-server"). "' }); });</script>
        ";
    }

    // get and set default if null
    static function getCookieServer(string $default = null)
    {
        $server = Cookie::get('current_server', $default);
        Cookie::queue("current_server", $server);
        return $server;
    }

    // get current server
    public static function getCurrentServer()
    {
        return Cookie::get('current_server');
    }

    public static function getCurrentServerOpenTime()
    {
        $server = self::getCurrentServer();
        $server = self::getServer($server);
        return $server->open_time;
    }

    public static function getCurrentServerOpenDays()
    {
        $server = self::getCurrentServer();
        $server = self::getServer($server);
        return intval((time() - $server->open_time) / 86400) + 1;
    }

    // server
    public static function getServer(string $server)
    {
        return DB::table("server_list")->where("server_node", $server)->orWhere("server_id", "LIKE", $server)->first();
    }

    // has server
    public static function hasServer(string $node)
    {
        return !is_null(self::getServer($node));
    }

    public static function nextServerId(string $type)
    {
        return DB::table("server_list")->where("server_type", $type)->max("server_id") + 1;
        // return intval(DB::selectOne("SELECT MAX(`server_id`) + 1 AS `server_id` FROM `server_list` WHERE `server_type` = '{$type}'")->server_id);
    }

    public static function nextServerPort(string $type)
    {
        return DB::table("server_list")->where("server_type", $type)->max("server_port") + 1;
        // return intval(DB::selectOne("SELECT MAX(`server_port`) + 1 AS `server_port` FROM `server_list` WHERE `server_type` = '{$type}'")->server_port);
    }

    // server list
    public static function getServerList(string $type = null)
    {
        if (!is_null($type) && !empty($type)) {
            return DB::table("server_list")->where("server_type", $type)->orderBy("server_id")->get()->toArray();
        } else {
            return DB::table("server_list")->orderBy("server_id")->get()->toArray();
        }
    }

    // send request
    public static function send(string $server = "this", string $command = "", string $data = "", string $method = "GET", int $timeout = 5)
    {
        if ($server == "all") {
            // get all current node-type's node
            $current_node = self::getServer(self::getCurrentServer());
            $server_list = self::getServerList($current_node->server_type);
        } else if ($server == "this")
            // get current node
            $server_list = [self::getServer(self::getCurrentServer())];
        else if(self::hasServer($server))
            $server_list = [self::getServer($server)];
        else
            return [trans("unknown_server") => $server];
        // send and get result
        $result = ["ok" => [], "error" => []];
        foreach ($server_list as $server)
        {
            $opts = [
                "http" => [
                    "method" => $method,
                    "timeout" => $timeout,
                    "header" => "Content-Type: application/x-www-form-urlencoded\r\nCommand: {$command}\r\nContent-Length: " . strlen($data) . "\r\n",
                    "content" => $data
                ]
            ];
            try {
                $result["ok"][$server->server_name] = file_get_contents("http://" . env("SERVER_URL", "127.0.0.1") . ":" . $server->server_port, false, stream_context_create($opts));
            } catch (Exception $exception) {
                $result["error"][$server->server_name] = $exception->getMessage();
            }
        }
        return $result;
    }

    // publish server list
    public static function getPublishServerList()
    {
        return array_map(function($server) {
            return [
                "server_name" => $server->server_name,
                "server_id" => $server->server_id,
                "server_ip" => $server->server_ip,
                "server_port" => $server->server_port,
                "tab_name" => $server->tab_name,
            ];
        }, self::getServerList("local"));
    }

    // reload static server list
    public static function publishServerList(string $path = "")
    {
        if ($path == "") $path = public_path("server-list.php");
        $list = self::getPublishServerList();
        $data =  "<?php header('content-type:application:json;charset=utf8');"  . "header('Access-Control-Allow-Origin: *');" . "header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');" . " echo '" . json_encode($list) . "';";
        file_put_contents($path, $data);
    }
}
