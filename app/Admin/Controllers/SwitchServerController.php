<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Throwable;

class SwitchServerController extends Controller
{
    private const connection = "GameDataMySQL";

    /** Switch server
     *
     * @return  RedirectResponse
     * @throws Throwable
     */
    public function switch(): RedirectResponse
    {
        $server = request()->input("server");
        Cookie::queue("current_server", $server);
        self::changeConnection($server);
        return back();
    }

    /** Nav bar server list
     *
     * @return string
     * @throws Throwable
     */
    public static function list(): string
    {
        $data = self::getServerList();
        if (!empty($data)) {
            // first as default
            $current = $data[0];
            // get and set current cookie server with default
            $current_server_node = self::getCookieServer($current->server_node);
            // change connection
            self::changeConnection($current_server_node);
            // build select list
            $list = "";
            foreach($data as $row) {
                if ($row->server_node === $current_server_node) {
                    $current = $row;
                    continue;
                }
                $list .= "<option value='$row->server_node'>$row->server_name</option>";
            }
            $list = "<option value='$current->server_node'>$current->server_name</option>" . $list;
        } else {
            $list = "";
        }
        return "
            <style>.server-select{margin: 8px 8px 0px 0px;}</style>
            <style>.select2-dropdown,.select2-dropdown--below{border:unset!important;box-shadow: 0 0 10px 0 rgb(0 0 0 / 20%);}</style>
            <li class='server-select'>
                <select class='form-control server-list' style='min-width:18em;outline:none;'>
                    $list
                </select>
            </li>
            <script>
                $(document).ready(function() { 
                    $('.server-list').select2({placeholder: ''}).on('change', function(){
                        $.pjax({container: '#pjax-container', url: 'switch-server?server=' + this.value});
                    });
                });
            </script>
        ";
    }

    /**
     * Get current cookie server
     *
     * @param string $default
     * @return string
     */
    static function getCookieServer(string $default = ''): string
    {
        $server = Cookie::get('current_server', $default);
        Cookie::queue("current_server", $server);
        return $server;
    }

    /**
     * Get current cookie server
     * @return string
     */
    public static function getCurrentServer(): string
    {
        return Cookie::get('current_server');
    }

    /**
     * Get current server open time
     * @return int
     */
    public static function getCurrentServerOpenTime():int
    {
        $server = self::getCurrentServer();
        $server = self::getServer($server);
        return $server->open_time;
    }

    /**
     * Get current server open days
     * @return int
     */
    public static function getCurrentServerOpenDays(): int
    {
        $server = self::getCurrentServer();
        $server = self::getServer($server);
        return intval((time() - $server->open_time) / 86400) + 1;
    }

    /**
     * Get current server from database
     *
     * @param string $server
     * @return object
     */
    public static function getServer(string $server): object
    {
        return DB::table("server_list")
            ->where("server_node", $server)
            ->orWhere("server_id", "LIKE", $server)
            ->first();
    }

    /**
     * Check server exists from database
     *
     * @param string $node
     * @return bool
     */
    public static function hasServer(string $node): bool
    {
        return !is_null(self::getServer($node));
    }

    /**
     * Next server id
     *
     * @param string $type
     * @return int
     */
    public static function nextServerId(string $type): int
    {
        return DB::table("server_list")->where("server_type", $type)->max("server_id") + 1;
    }

    /**
     * Next server port
     *
     * @param string $type
     * @return int
     */
    public static function nextServerPort(string $type): int
    {
        return DB::table("server_list")->where("server_type", $type)->max("server_port") + 1;
    }

    /**
     * Get server list from database
     *
     * @param string|null $type
     * @return array
     */
    public static function getServerList(string $type = null): array
    {
        if (!empty($type)) {
            return DB::table("server_list")
                ->where("server_type", $type)
                ->orderBy("id")
                ->get()
                ->toArray();
        } else {
            return DB::table("server_list")
                ->orderBy("id")
                ->get()
                ->toArray();
        }
    }

    /**
     * Send request
     * 
     * @param string $server
     * @param string $command
     * @param array $data
     * @param string $method
     * @param int $timeout
     * @return array
     */
    public static function send(string $server = "this", string $command = "", array $data = [], string $method = "POST", int $timeout = 5): array
    {
        if ($server == "all") {
            // get all current node-type's node
            $current_node = self::getServer(self::getCurrentServer());
            $server_list = self::getServerList($current_node->server_type);
        } else if ($server == "this") {
            // get current node
            $server_list = [self::getServer(self::getCurrentServer())];
        } else if(self::hasServer($server)) {
            $server_list = [self::getServer($server)];
        } else {
            return [trans("admin.unknown_server") => $server];
        }
        // send and get result
        $result = ["ok" => [], "error" => []];
        foreach ($server_list as $server) {
            try {
                $url = "$server->server_host:$server->server_port";
                if ($method == 'POST') {
                    $response = Http::timeout($timeout)->post($url, ["command" => $command, "data" => $data]);
                } else {
                    $response = Http::timeout($timeout)->get($url, ["command" => $command, "data" => $data]);
                }
                $response->throw();
                $result["ok"][$server->server_name] = $response->body();
            } catch (Exception $exception) {
                $result["error"][$server->server_name] = $exception->getMessage();
            }
        }
        return $result;
    }

    /**
     * execute maker command
     * 
     * @param string $local
     * @param string $remote
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function pushFile(string $local, string $remote, string $output = "stdout"): string
    {
        $server = self::getServer(self::getCurrentServer());
        if (empty($server->ssh_alias)) {
            // local machine
            $process = new Process(["cp", $local, "$server->server_root/$remote"], $server->server_root);
        } else {
            // remote machine
            $pass = [base_path("vendor/bin/sshpass"), $server->ssh_pass];
            $scp = ["scp", "-o", "LogLevel=error", "-o", "StrictHostKeyChecking=no", "-o", "UserKnownHostsFile=/dev/null", "-C"];
            $file = [$local, "$server->ssh_alias:$server->server_root/$remote"];
            $process = new Process(array_merge($pass, $scp, $file));
        }
        $process->run();
        // result
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        if ($output == "stdout") {
            return $process->getOutput();
        } else if ($output == "stderr") {
            return $process->getErrorOutput();
        } else {
            throw new Exception("Unknown output: $output", 1);
        }
    }

    /**
     * execute maker command
     * 
     * @param string $remote
     * @param string $local
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function pullFile(string $remote, string $local, string $output = "stdout"): string
    {
        $server = self::getServer(self::getCurrentServer());
        if (empty($server->ssh_alias)) {
            // local machine
            $process = new Process(["cp", "$server->server_root/$remote", $local], $server->server_root);
        } else {
            // remote machine
            $pass = [base_path("vendor/bin/sshpass"), $server->ssh_pass];
            $scp = ["scp", "-o", "LogLevel=error", "-o", "StrictHostKeyChecking=no", "-o", "UserKnownHostsFile=/dev/null", "-C"];
            $file = ["$server->ssh_alias:$server->server_root/$remote", $local];
            $process = new Process(array_merge($pass, $scp, $file));
        }
        $process->run();
        // result
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        if ($output == "stdout") {
            return $process->getOutput();
        } else if ($output == "stderr") {
            return $process->getErrorOutput();
        } else {
            throw new Exception("Unknown output: $output", 1);
        }
    }

    /**
     * execute maker command
     *
     * @param array $command
     * @param string|null $path
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function executeMakerScript(array $command, string $path = null, string $output = "stdout"): string
    {
        $server = self::getServer(self::getCurrentServer());
        $path = empty($path) ? $server->server_root : $path;
        if (empty($server->ssh_alias)) {
            // local machine
            $script = ["$server->server_root/script/shell/maker.sh"];
            $process = new Process(array_merge($script, $command), $path);
        } else {
            // remote machine
            $pass = [base_path("vendor/bin/sshpass"), $server->ssh_pass];
            $ssh = ["ssh", "-o", "LogLevel=error", "-o", "StrictHostKeyChecking=no", "-o", "UserKnownHostsFile=/dev/null", "-C", $server->ssh_alias];
            $script = ["cd", $path, "&&", "$server->server_root/script/shell/maker.sh"];
            $process = new Process(array_merge($pass, $ssh, $script, $command));
        }
        $process->run();
        // result
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        if ($output == "stdout") {
            return $process->getOutput();
        } else if ($output == "stderr") {
            return $process->getErrorOutput();
        } else {
            throw new Exception("Unknown output: $output", 1);
        }
    }

    /**
     * execute run command
     *
     * @param array $command
     * @param string|null $path
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function executeRunScript(array $command, string $path = null, string $output = "stdout"): string
    {
        $server = self::getServer(self::getCurrentServer());
        $path = empty($path) ? $server->server_root : $path;
        if (empty($server->ssh_alias)) {
            // local machine
            $script = ["$server->server_root/script/shell/run.sh"];
            $process = new Process(array_merge($script, $command), $path);
        } else {
            // remote machine
            $pass = [base_path("vendor/bin/sshpass"), $server->ssh_pass];
            $ssh = ["ssh", "-o", "LogLevel=error", "-o", "StrictHostKeyChecking=no", "-o", "UserKnownHostsFile=/dev/null", "-C", $server->ssh_alias];
            $script = ["cd", $path, "&&", "$server->server_root/script/shell/run.sh"];
            $process = new Process(array_merge($pass, $ssh, $script, $command));
        }
        $process->run();
        // result
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        if ($output == "stdout") {
            return $process->getOutput();
        } else if ($output == "stderr") {
            return $process->getErrorOutput();
        } else {
            throw new Exception("Unknown output: $output", 1);
        }
    }

    /**
     * Publish server list
     *
     * @return array
     */
    public static function getPublishServerList(): array
    {
        return array_map(function($server) {
            return [
                "server_name" => $server->server_name,
                "server_id" => $server->server_id,
                "server_host" => $server->server_host,
                "server_ip" => $server->server_ip,
                "server_port" => $server->server_port,
                "tab_name" => $server->tab_name,
                "state" => $server->state,
            ];
        }, self::getServerList("local"));
    }

    // reload static server list
    public static function publishServerList(string $path = "")
    {
        if ($path == "") {
            $path = public_path("server-list.php");
        }
        $list = json_encode(self::getPublishServerList());
        $data =  "<?php 
            header('content-type:application:json;charset=utf8');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
            echo '$list'
        ?>";
        file_put_contents($path, $data);
    }

    /**
     * Change game data connection
     *
     * @param string $server
     * @throws Throwable
     */
    public static function changeConnection(string $server)
    {
        $server = self::getServer($server);
        // get database config
        $database = app('config')->get('database');
        // chose this connection
        $data = $database['connections'][self::getConnection()];
        // modify config
        $data["host"] = $server->db_host;
        $data["port"] = $server->db_port;
        $data["database"] = $server->db_name;
        // store connection
        $database['connections']['GameDataMySQL'] = $data;
        // save database config
        app('config')->set('database', $database);
        // reconnect
        DB::connection(self::getConnection())->reconnect();
    }

    /**
     * Get game data connection
     *
     * @return string
     */
    public static function getConnection(): string
    {
        return self::connection;
    }

    /**
     * Get connection DB interface
     *
     * @return ConnectionInterface
     */
    public static function getDB(): ConnectionInterface
    {
        return DB::connection(self::connection);
    }
}
