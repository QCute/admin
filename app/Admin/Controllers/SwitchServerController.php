<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ServerListController;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PDO;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SwitchServerController extends Controller
{
    /**
     * Switch server
     *
     * @return  RedirectResponse
     */
    public function switch(): RedirectResponse
    {
        $reload = request()->input("reload");
        if ($reload) {
            ServerListController::reload();
            return back();
        }
        $server = request()->input("server");
        Cookie::queue("current_server", $server);
        self::changeConnection($server);
        return back();
    }

    /**
     * Nav bar server list
     *
     * @return string
     */
    public static function list(): string
    {
        $data = self::getServerList();
        if (empty($data)) {
            $list = "";
        } else {
            // first as default
            $current = self::getCookieServer($data[0]->server_node);
            // change connection
            self::changeConnection($current);
            // build select list
            $list = "";
            foreach ($data as $row) {
                if ($row->server_node === $current) {
                    $current = $row;
                } else {
                    $list = "$list<option value='$row->server_node'>$row->server_name</option>";
                }
            }
            $list = "<option value='$current->server_node'>$current->server_name</option>$list";
        }
        $url = "switch-server?server=";
        $prefix = config('admin.route.prefix');
        if (!empty($prefix)) {
            $url = "/$prefix/$url";
        }
        return "
            <style>.server-select{margin: 8px 8px 0px 0px;}</style>
            <style>.select2-dropdown,.select2-dropdown--below{border:unset!important;box-shadow: 0 0 10px 0 rgb(0 0 0 / 20%);}</style>
            <li class='server-select'><select class='form-control server-list' style='min-width:18em;outline:none;'>$list</select></li>
            <li class='server-select'>
                 <div class='btn-group server-refresh'>
                    <label class='btn btn-info'>
                        <i class='fa fa-send'></i>
                    </label>
                </div>
            </li>
            <script>
                $(document).ready(function() { 
                    $('.server-list').select2({placeholder: ''}).on('change', function(){
                        $.pjax({container: '#pjax-container', url: '$url' + this.value});
                    });
                    $('.server-refresh').on('click', function(){
                        $.pjax({container: '#pjax-container', url: '$url' + $('.server-list').val() + '&reload=true'});
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
     *
     * @return string|null
     */
    public static function getCurrentServer(): string|null
    {
        return Cookie::get('current_server');
    }

    /**
     * Get current server open time
     *
     * @return int
     */
    public static function getCurrentServerOpenTime(): int
    {
        $server = self::getCurrentServer();
        $server = self::getServer($server);
        return $server->open_time;
    }

    /**
     * Get current server open days
     *
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
     * @param int|string $server
     * @return object|null
     */
    public static function getServer(mixed $server): object|null
    {
        return DB::table("server_list")
            ->where("server_node", $server)
            ->orWhere("server_id", $server)
            ->limit(1)
            ->first();
    }

    /**
     * Check server exists from database
     *
     * @param int|string $server
     * @return bool
     */
    public static function hasServer(mixed $server): bool
    {
        return !is_null(self::getServer($server));
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
     * @param array $columns
     * @return array
     */
    private static function getServerList(string $type = null, array $columns = ['*']): array
    {
        if (empty($type)) {
            return DB::table("server_list")
                ->orderBy("id")
                ->get($columns)
                ->toArray();
        } else {
            return DB::table("server_list")
                ->where("server_type", $type)
                ->orderBy("id")
                ->get($columns)
                ->toArray();
        }
    }

    /**
     * Send request
     *
     * @param object|string $server
     * @param string $command
     * @param array $data
     * @param string $method
     * @param int $timeout
     * @return array
     */
    public static function send(mixed $server = "current", string $command = "", array $data = [], string $method = "POST", int $timeout = 60): array
    {
        if (is_object($server)) {
            // server
            $server_list = [$server];
        } else if ($server == "all") {
            // get all current node-type's node
            $current = self::getServer(self::getCurrentServer());
            $server_list = self::getServerList($current->server_type);
        } else if ($server == "current") {
            // get current server
            $server_list = [self::getServer(self::getCurrentServer())];
        } else if (self::hasServer($server)) {
            // this server
            $server_list = [self::getServer($server)];
        } else {
            return [trans("admin.unknown_server") => $server];
        }
        // send and get result
        $result = ["ok" => [], "error" => []];
        foreach ($server_list as $server) {
            try {
                $url = "$server->server_host:$server->server_port";
                if ($method == "POST") {
                    $response = Http::withHeaders(["Cookie" => $server->server_cookie])->timeout($timeout)->post($url, ["command" => $command, "data" => $data]);
                } else {
                    $response = Http::withHeaders(["Cookie" => $server->server_cookie])->timeout($timeout)->get($url, ["command" => $command, "data" => $data]);
                }
                $result["ok"][$server->server_name] = $response->throw()->body();
            } catch (Exception $exception) {
                $result["error"][$server->server_name] = $exception->getMessage();
            }
        }
        return $result;
    }

    /**
     * Push file to remote
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
        // remote or local
        if (empty($server->ssh_host)) {
            // local machine
            return self::executeLocal(["cp", $local, "$server->server_root/$remote"]);
        } else {
            // remote machine
            $command = [$local, "$server->ssh_host:$server->server_root/$remote"];
            return self::executeRemote($command, $server->ssh_pass, "scp", $output);
        }
    }

    /**
     * Pull file from remote
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
        // remote or local
        if (empty($server->ssh_host)) {
            // local machine
            return self::executeLocal(["cp", "$server->server_root/$remote", $local], $output);
        } else {
            // remote machine
            $command = ["$server->ssh_host:$server->server_root/$remote", $local];
            return self::executeRemote($command, $server->ssh_pass, "scp", $output);
        }
    }

    /**
     * Execute command
     *
     * @param array $command
     * @param string|null $path
     * @param object|null $config
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function execute(array $command, string $path = null, object $config = null, string $output = "stdout"): string
    {
        $server = self::getServer(self::getCurrentServer());
        // server path as default
        $path = empty($path) ? $server->server_root : $path;
        // remote or local
        if (empty($server->ssh_host)) {
            // local machine
            return self::executeLocal($command, $path);
        } else {
            // remote machine
            $ssh_host = empty($config) ? $server->ssh_host : $config->Host;
            $ssh_pass = empty($config) ? $server->ssh_pass : $config->Password;
            // concat ssh command
            $command = implode(" ", array_merge(["cd", $path, "&&"], $command));
            // set host and command
            $command = ["-C", $ssh_host, $command];
            return self::executeRemote($command, $ssh_pass, "ssh", $output);
        }
    }

    /**
     * Execute command
     *
     * @param array $command
     * @param string|null $path
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function executeLocal(array $command, string $path = null, string $output = "stdout"): string
    {
        // fill PATH and SSH_AUTH_SOCK env variables
        $env = ["PATH" => getenv("PATH"), "SSH_AUTH_SOCK" => getenv("SSH_AUTH_SOCK")];
        // default timeout 10 seconds
        $process = new Process($command, $path, $env, null, env('PROCESS_TIMEOUT', 10));
        return self::runProcess($process, $output);
    }

    /**
     * Execute command
     *
     * @param array $command
     * @param string $ssh_pass
     * @param string $program
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function executeRemote(array $command, string $ssh_pass = "", string $program = "", string $output = "stdout"): string
    {
        $pass = [base_path("vendor/bin/sshpass"), $ssh_pass];
        $ssh = [$program, "-o", "LogLevel=error", "-o", "StrictHostKeyChecking=no", "-o", "UserKnownHostsFile=/dev/null"];
        $process = new Process(array_merge($pass, $ssh, $command), null, null, null, env('PROCESS_TIMEOUT', 10));
        return self::runProcess($process, $output);
    }

    /**
     * Execute command
     *
     * @param Process $process
     * @param string $output
     * @return string
     * @throws Exception
     */
    private static function runProcess(Process $process, string $output = "stdout"): string
    {
        // turning on PTY support, need by ssh, git/svn etc...
        // reference https://symfony.com/doc/current/components/process.html#using-tty-and-pty-modes
        $process->setPty(true);
        // run equals start and wait
        $process->run();
        // handle result
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
     * Execute maker command
     *
     * @param array $command
     * @param string|null $path
     * @param object|null $config
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function executeMakerScript(array $command, string $path = null, object $config = null, string $output = "stdout"): string
    {
        $script = array_merge(["script/shell/maker.sh"], $command);
        return self::execute($script, $path, $config, $output);
    }

    /**
     * Execute run command
     *
     * @param array $command
     * @param string|null $path
     * @param object|null $config
     * @param string $output
     * @return string
     * @throws Exception
     */
    public static function executeRunScript(array $command, string $path = null, object $config = null, string $output = "stdout"): string
    {
        $script = array_merge(["script/shell/run.sh"], $command);
        return self::execute($script, $path, $config, $output);
    }

    /**
     * Get SSH Configure
     *
     * @return array
     * @throws Exception
     */
    public static function getSSHConfig(): array
    {
        $file = getenv('HOME') . "/.ssh/config";
        if (!file_exists($file)) {
            return [];
        }
        $host = "";
        $config = [];
        $data = explode("\n", file_get_contents($file));
        foreach ($data as $line) {
            // empty
            if (empty($line)) continue;
            // comment
            if (str_starts_with(trim($line), "#")) continue;
            // config
            if (!preg_match("/(\w+)(?:\s*=\s*|\s+)(.+)/", trim($line), $matches)) {
                throw new Exception("Invalid Config File Syntax");
            }
            [, $key, $value] = $matches;
            if ($key === "Host") {
                // save
                $host = $value;
                // check duplicate
                if (isset($config[$host])) {
                    throw new Exception("Duplicate Host: $value");
                }
                // save
                $config[$host] = (object)[$key => $value];
            } else {
                $object = $config[$host];
                $object->{$key} = $value;
            }
        }
        return $config;
    }

    /**
     * Publish server list
     *
     * @return array
     */
    public static function getPublishServerList(): array
    {
        $column = [
            "server_name",
            "server_id",
            "server_host",
            "server_port",
        ];
        return self::getServerList("local", $column);
    }

    /**
     * Get game data connection
     *
     * @return string
     */
    public static function getConnection(): string
    {
        return "game";
    }

    /**
     * Get connection DB interface
     *
     * @return ConnectionInterface
     */
    public static function getDB(): ConnectionInterface
    {
        return DB::connection(self::getConnection());
    }

    /**
     * Change game data connection
     *
     * @param int|string|object $server
     * @return ConnectionInterface
     */
    public static function changeConnection(mixed $server): ConnectionInterface
    {
        $server = is_object($server) ? $server : self::getServer($server);
        $name = self::getConnection();
        $config = Config::get("database.connections.$name");
        // replace with pdo
        $pdo = new PDO("{$config["driver"]}:host=$server->db_host;port=$server->db_port;dbname=$server->db_name;charset={$config["charset"]}", $server->db_username, $server->db_password, [PDO::ATTR_PERSISTENT => true]);
        $connection = DB::connection($name);
        $connection->setPdo($pdo);
        return $connection;
    }
}
