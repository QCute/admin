<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ServerListController;
use Encore\Admin\Admin;
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

class SwitchServerController extends Admin
{
    /**
     * Nav bar server list
     *
     * @return string
     */
    public function list(): string
    {
        // collect server list from channels
        $channels = $this->getRoleChannels();
        $list = self::getServerList(function($table) use ($channels) { return $table->whereIn('channel', $channels); });
        $data = array_reduce($list, function($acc, $item) {
            $group = $acc[$item->channel] ?? (object)["channel" => $item->channel, "channel_name" => $item->channel_name, "server_list" => []];
            $group->server_list[$item->server_node] = $item;
            $acc[$item->channel] = $group;
            return $acc;
        }, []);
        // server
        $server_list = self::makeServerlList($data);
        // channel
        $channel_list = self::makeChannelList($data);
        // node
        $node_list = self::makeNodeList($data);
        // send
        $send = self::makeSend();
        // right side html
        return $server_list . $channel_list . $node_list . $send;
    }

    /**
     * Nav bar server list
     *
     * @return string
     */
    public static function makeServerlList(array $data): string
    {
        $channel_list = [];
        foreach($data as $name => $channel) {
            $server_list = [];
            foreach($channel->server_list as $key => $server) {
                $server_list[$key] = (object)["id" => $server->server_node, "text" => $server->server_name];
            }
            $channel_list[$name] = (object)["id" => $channel->channel, "text" => $channel->channel_name, "server_list" => $server_list];
        }
        $server = json_encode($channel_list);
        return <<<HTML
        <script>
            const server = $server;
        </script>
HTML;
    }

    /**
     * Nav bar channel list
     *
     * @return string
     */
    public static function makeChannelList(array $data): string
    {
        if(empty($data)) return "";

        // channel
        $current = Cookie::get('current_channel', array_key_first($data));
        Cookie::queue("current_channel", $current);

        // current channel
        $current = $data[$current];
        unset($data[$current->channel]);

        // set current to fist
        $data = array_values($data);
        array_unshift($data, $current);

        // make select json
        $data = json_encode(array_map(function($value) {
            return (object)["id" => $value->channel, "text" => $value->channel_name];
        }, array_values($data)));

        // path
        $path = "/switch/channel?channel=";
        // add prefix
        $prefix = config('admin.route.prefix');
        $path = empty($prefix) ? $path : "/$prefix" . $path;

        return <<<HTML
            <style>.channel-label{ margin: 16px 8px 0px 0px; }</style>
            <style>.channel-select{ margin: 8px 8px 0px 0px; }</style>
            <style>.select2-dropdown,.select2-dropdown--below{ border: unset!important; box-shadow: 0 0 10px 0 rgb(0 0 0 / 20%); }</style>
            <li class='channel-label'>
                <label for="channel-select" class="asterisk control-label">渠道: </label>
            </li>
            <li class='channel-select'>
                <select name='channel-select' class='form-control channel-list' style='min-width:18em; outline:none;'></select>
            </li>
            <script>
                $(document).ready(function() { 
                    setChannelList($data);
                });
                function setChannelList(data) {
                    $(".channel-list").empty();
                    $('.channel-list').select2({ data, placeholder: '' }).off('change', onChannelChange).on('change', onChannelChange);
                }
                async function onChannelChange() {
                    await $.pjax({ container: '#pjax-container', url: '$path' + this.value });
                    setServerList(Object.values(server[this.value].server_list));
                }
            </script>
HTML;
    }

    /**
     * Nav bar node list
     *
     * @return string
     */
    public static function makeNodeList(array $data): string
    {
        if(empty($data)) return "";
        
        // channel
        $current = Cookie::get('current_channel', array_key_first($data));
        Cookie::queue("current_channel", $current);
        
        $data = $data[$current]->server_list;

        // server
        $current = Cookie::get('current_server_node', array_key_first($data));
        Cookie::queue("current_server_node", $current);

        // current server
        $current = $data[$current];
        unset($data[$current->server_node]);

        // change conenction
        self::changeConnection($current);

        // set current to fist
        $data = array_values($data);
        array_unshift($data, $current);

        // make select json
        $data = json_encode(array_map(function($value) {
            return (object)["id" => $value->server_node, "text" => $value->server_name];
        }, array_values($data)));

        // path
        $path = "/switch/node?node=";
        // add prefix
        $prefix = config('admin.route.prefix');
        $path = empty($prefix) ? $path : "/$prefix" . $path;

        return <<<HTML
            <style>.node-label{ margin: 16px 8px 0px 0px; }</style>
            <style>.node-select{ margin: 8px 8px 0px 0px; }</style>
            <style>.select2-dropdown,.select2-dropdown--below{ border: unset!important; box-shadow: 0 0 10px 0 rgb(0 0 0 / 20%); }</style>
            <li class='node-label'>
                <label for="node-select" class="asterisk control-label">服务器: </label>
            </li>
            <li class='node-select'>
                <select class='form-control node-list' style='min-width:18em; outline:none;'></select>
            </li>
            <script>
                $(document).ready(function() { 
                    setServerList($data);
                });
                function setServerList(data) {
                    $(".node-list").empty();
                    $('.node-list').select2({ data, placeholder: '' }).off('change', onNodeChange).on('change', onNodeChange);
                }
                function onNodeChange() {
                    $.pjax({ container: '#pjax-container', url: '$path' + this.value });
                }
            </script>
HTML;
    }

    public static function makeSend(): string
    {
        // path
        $path = "/reload/server";
        // add prefix
        $prefix = config('admin.route.prefix');
        $path = empty($prefix) ? $path : "/$prefix" . $path;

        return <<<HTML
            <style>.send-btn{ margin: 8px 8px 0px 0px; }</style>
            <li class='send-btn'>
                 <div class='btn-group server-refresh'>
                    <label class='btn btn-info'>
                        <i class='fa fa-send'></i>
                    </label>
                </div>
            </li>
            <script>
                $(document).ready(function() { 
                    $('.server-refresh').on('click', function(){
                        $.pjax({container: '#pjax-container', url: '$path' });
                    });
                });
            </script>
HTML;
    }

    /**
     * Switch channel
     *
     * @return RedirectResponse
     */
    public function switchChannel(): RedirectResponse
    {
        // channel
        $channel = request()->input("channel");
        Cookie::queue("current_channel", $channel);

        [$server, ] = self::getServerList(["channel" => $channel, "server_type" => "local"]);

        if($server) {
            
            // set server node
            Cookie::queue("current_server_node", $server->server_node);

            // change connection
            self::changeConnection($server);

        } else {
            // remove server node
            Cookie::unqueue("current_server_node");
        }

        return back();
    }

    /**
     * Switch server
     *
     * @return RedirectResponse
     */
    public function switchNode(): RedirectResponse
    {
        // server node
        $node = request()->input("node");
        Cookie::queue("current_server_node", $node);

        // change connection
        $channel = self::getCurrentChannel();
        $server = self::getServer($channel, $node);
        self::changeConnection($server);

        return back();
    }

    /**
     * Switch server
     *
     * @return RedirectResponse
     */
    public function reloadServer(): RedirectResponse
    {   
        ServerListController::reload();   
        return back();
    }

    /**
     * Get role channels
     *
     * @return array
     */
    public function getRoleChannels(): array {
        $user = $this->user();
        if(empty($user)) {
            return [];
        }

        // collect role ids from current user
        $ids = array_map(function($role) { return $role['id']; }, $user->roles->toArray());
        
        // collect channels from role ids
        $channels = DB::table('admin_role_channels')->whereIn('role_id', $ids)->select('channel')->get()->toArray();
        $channels = array_map(function($role) { return $role->channel; }, $channels);

        return $channels;
    }
    
    /**
     * Has channel
     *
     * @return bool
     */
    public function hasChannel(string $channel): bool {
        // collect role ids from current user
        $ids = array_map(function($role) { return $role['id']; }, $this->user()->roles->toArray());
        
        // collect channels from role ids
        $channels = DB::table('admin_role_channels')->whereIn('role_id', $ids)->where('channel', $channel)->limit(1)->get();
        return !empty($channels);
    }

    /**
     * Get current cookie channel
     *
     * @return string|null
     */
    public static function getCurrentChannel(): string|null
    {
        return Cookie::get('current_channel');
    }

    /**
     * Get current cookie server
     *
     * @return string|null
     */
    public static function getCurrentServerNode(): string|null
    {
        return Cookie::get('current_server_node');
    }

    /**
     * Get current server open time
     *
     * @return int
     */
    public static function getCurrentServerOpenTime(): int
    {
        $channel = SwitchServerController::getCurrentChannel();
        $node = SwitchServerController::getCurrentServerNode();
        $server = SwitchServerController::getServer($channel, $node);
        return $server->open_time;
    }

    /**
     * Get current server open days
     *
     * @return int
     */
    public static function getCurrentServerOpenDays(): int
    {
        $channel = SwitchServerController::getCurrentChannel();
        $node = SwitchServerController::getCurrentServerNode();
        $server = SwitchServerController::getServer($channel, $node);
        return intval((time() - $server->open_time) / 86400) + 1;
    }

    /**
     * Get current server from database
     *
     * @param string $channel
     * @param string $node
     * @return object|null
     */
    public static function getServer(string $channel, string $node): object|null
    {
        // todo check has channel
        return DB::table("server_list")
            ->where("channel", $channel)
            ->where("server_node", $node)
            ->limit(1)
            ->first();
    }
    
    /**
     * Get server list from database
     *
     * @param mixed $filter
     * @param array $columns
     * @return array
     */
    public static function getServerList(mixed $filter = [], array $columns = ['*']): array
    {
        $table = DB::table("server_list");
        if(is_callable($filter)) {
            $filter($table);
        } else {
            foreach($filter as $name => $value) {
                $table->where($name, $value);
            }
        }
        return $table->orderBy("id", "ASC")->get($columns)->toArray();
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
            $channel = self::getCurrentChannel();
            $node = self::getCurrentServerNode();
            $server = self::getServer($channel, $node);
            $server_list = self::getServerList(["server_type" => $server->server_type]);
        } else if ($server == "channel") {
            // get all current channel and node-type's node
            $channel = self::getCurrentChannel();
            $node = self::getCurrentServerNode();
            $server = self::getServer($channel, $node);
            $server_list = self::getServerList(["channel" => $server->channel, "server_type" => $server->server_type]);
        } else if ($server == "current") {
            // get current server
            $channel = self::getCurrentChannel();
            $node = self::getCurrentServerNode();
            $server = self::getServer($channel, $node);
            $server_list = [$server];
        } else {
            return [trans("admin.unknown_server") => $server];
        }

        // send and get result
        $result = ["succeeded" => [], "failed" => [], "error" => []];
        foreach ($server_list as $server) {
            try {
                $url = "$server->server_host:$server->server_port";
                if ($method == "POST") {
                    $response = Http::withHeaders(["Cookie" => $server->server_cookie])->timeout($timeout)->post("$url/$command", $data);
                } else {
                    $response = Http::withHeaders(["Cookie" => $server->server_cookie])->timeout($timeout)->get("$url/$command", $data);
                }
                $json = $response->json();
                $key = $json["result"] == "ok" ? "succeeded" : "failed";
                $result[$key][$server->server_name] = $json;
            } catch (Exception $exception) {
                $result["error"][$server->server_name] = $exception->getMessage();
            }
        }

        return $result;
    }

    public static function handleSendResult(array $array)
    {
        // succeeded
        $succeeded = implode("", array_map(function ($k, $v) {
            return "$k:<br/>→ " . json_encode($v) . "<br/><br/>";
        }, array_keys($array["succeeded"]), array_values($array["succeeded"])));
        
        // fail
        $failed = implode("", array_map(function ($k, $v) {
            return "$k:<br/>→ " . json_encode($v) . "<br/><br/>";
        }, array_keys($array["failed"]), array_values($array["failed"])));
        
        // error
        $error = implode("", array_map(function ($k, $v) {
            return "$k:<br/>→ " . json_encode($v) . "<br/><br/>";
        }, array_keys($array["error"]), array_values($array["error"])));

        // succeeded
        if (!empty($succeeded)) {
            admin_success(trans("admin.succeeded"), $succeeded);
        }
        
        // failed
        if (!empty($failed)) {
            admin_warning(trans("admin.failed"), $failed);
        }

        // error
        if (!empty($error)) {
            admin_error(trans("admin.error"), $error);
        }
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
        $channel = self::getCurrentChannel();
        $node = self::getCurrentServerNode();
        $server = self::getServer($channel, $node);
        // remote or local
        if (empty($server->ssh_host)) {
            // local machine
            if (file_exists($local)) {
                copy($local, "$server->server_root/$remote");
                return "";
            } else {
                return "no such file or directory";
            }
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
        $channel = self::getCurrentChannel();
        $node = self::getCurrentServerNode();
        $server = self::getServer($channel, $node);
        // remote or local
        if (empty($server->ssh_host)) {
            // local machine
            if (file_exists("$server->server_root/$remote")) {
                copy("$server->server_root/$remote", $local);
                return "";
            } else {
                return "no such file or directory";
            }
        } else {
            // remote machine
            $command = ["$server->ssh_host:$server->server_root/$remote", $local];
            return self::executeRemote($command, $server->ssh_pass, "scp", $output);
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
        $channel = self::getCurrentChannel();
        $node = self::getCurrentServerNode();
        $server = self::getServer($channel, $node);
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
        // default timeout 10 seconds
        $process = new Process($command, $path, ["PATH" => getenv("PATH")], null, env('PROCESS_TIMEOUT', 10));
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
            $pos = strpos($line, "#");
            $line = trim(substr($line, 0, is_bool($pos) ? strlen($line) : $pos));
            // empty
            if (empty($line)) continue;
            // config
            if (is_bool(preg_match("/(\w+)(\s*=\s*|\s+)(.+)/", $line, $matches))) {
                throw new Exception("Invalid Config File Syntax");
            }
            [, $key, , $value] = $matches;
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
        return self::getServerList(["server_type" => "local"], $column);
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
     * Fold DB
     * get method must use keyBy after get
     * 
     * @param \Closure $get
     * @param \Closure $update
     * @return array
     */
    public static function foldDB(\Closure $get, \Closure $update): array
    {
        $list = self::getServerList();
        $acc = [];
        foreach($list as $server) {
            $db = self::changeConnection($server);
            $item = $get($db);
            if(empty($acc)) {
                $acc = $item;
                continue;
            }
            foreach($item as $key => $row) {
                // insert or update
                $acc[$key] = isset($acc[$key]) ? $update($acc[$key], $row) : $row;
            }
        }
        return $acc;
    }
    
    /**
     * Change game data connection
     *
     * @param object $server
     * @return ConnectionInterface
     */
    public static function changeConnection(object $server): ConnectionInterface
    {
        $name = self::getConnection();
        $config = Config::get("database.connections.$name");
        // todo optimize same connection
        // replace with pdo
        $pdo = new PDO("{$config["driver"]}:host=$server->db_host;port=$server->db_port;dbname=$server->db_name;charset={$config["charset"]}", $server->db_username, $server->db_password, [PDO::ATTR_PERSISTENT => true]);
        $connection = DB::connection($name);
        $connection->setDatabaseName($server->db_name);
        $connection->setPdo($pdo);
        return $connection;
    }
}
