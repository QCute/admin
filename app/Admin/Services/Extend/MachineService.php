<?php

namespace App\Admin\Services\Extend;

use App\Admin\Models\Extend\ServerModel;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class MachineService
{
    /**
     * Send request
     *
     * @param string|ServerModel $server 'ALL'|'CHANNEL'|'SERVER'|ServerModel
     * @param string $command
     * @param array $data
     * @param string $method
     * @param int $timeout
     * @return array
     */
    public static function send(string|ServerModel $server, string $command = '', array $data = [], string $method = 'POST', int $timeout = 60): array
    {
        // server set
        $servers = [];
        switch($server) {
            case 'ALL': {
                $servers = ServerService::getServers();
            };break;
            case 'CHANNEL': {
                $servers = ChannelService::getChannel()->servers;
            };break;
            case 'SERVER': {
                $servers = [ServerService::getServer()];
            };break;
            default: {
                if(!($server instanceof ServerModel)) {
                    throw new Exception('Invalid server');
                }
                $servers = [$server];
            }
        }

        // result set
        $result = [
            'succeeded' => [], 
            'failed' => [], 
            'error' => []
        ];

        // send and handle result
        foreach ($servers as $server) {
            try {
                $url = "$server->server_host:$server->server_port";
                $method = strtolower($method);
                $json = Http::withHeaders([
                        'Cookie' => $server->cookie
                    ])
                    ->timeout($timeout)
                    ->{$method}("$url/$command", $data)
                    ->json();

                // split result
                $key = $json['result'] == 'ok' ? 'succeeded' : 'failed';
                $result[$key][$server->node] = $json;
            } catch (Exception $exception) {
                $result['error'][$server->node] = $exception->getMessage();
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
     * @return array
     * @throws Exception
     */
    public static function pushFile(string $local, string $remote, string $output = 'stdout'): array
    {
        $server = ServerService::getServer();

        if(is_null($server)) {
            return ['code' => 1, 'msg' => 'Could not found server'];
        }

        // local machine
        if ($server->ssh_host === '') {

            if (!file_exists($local)) {
                return ['code' => 2, 'msg' => 'no such file or directory'];
            }

            // copy file
            copy($local, "$server->server_root/$remote");
            return ['code' => 0, 'msg' => ''];
        }

        // remote machine
        $command = [$local, "$server->ssh_host:$server->server_root/$remote"];
        return self::executeRemote('scp', $command, $server->ssh_pass, $output);
    }

    /**
     * Pull file from remote
     *
     * @param string $remote
     * @param string $local
     * @param string $output
     * @return array
     * @throws Exception
     */
    public static function pullFile(string $remote, string $local, string $output = 'stdout'): array
    {
        $server = ServerService::getServer();

        if(is_null($server)) {
            return ['code' => 1, 'msg' => 'Could not found server'];
        }

        // local machine
        if ($server->ssh_host === '') {

            if (!file_exists("$server->server_root/$remote")) {
                return ['code' => 2, 'msg' => 'no such file or directory'];
            }

            // copy file
            copy("$server->server_root/$remote", $local);
            return ['code' => 0, 'msg' => ''];
        } 

        // remote machine
        $command = ["$server->ssh_host:$server->server_root/$remote", $local];
        return self::executeRemote('scp', $command, $server->ssh_pass, $output);

    }

    /**
     * Execute get repository status
     *
     * @param ServerModel $server
     * @param string|null $path
     * @param string $output
     * @return array
     * @throws Exception
     */
    public static function repositoryStatus(ServerModel $server, string|null $path = null, string $output = 'stdout'): array
    {
        $status = '';

        try {
            $status .= MachineService::execute($server, ['git', 'status', '--porcelain'], $path, $output);
        } catch (Exception) {
            // suppress error when dir not a git repository
        }

        try {
            $status .= MachineService::execute($server, ['svn', 'status'], $path, $output);
        } catch (Exception) {
            // suppress error when dir not a svn repository
        }

        $array = [];
        $data = explode("\n", $status);
        foreach ($data as $row) {

            $row = preg_replace("/\s+/", ',', trim($row));
            if ($row === '') continue;
            [$mode, $file] = explode(',', $row);

            switch ($mode) {
                case 'A':
                    $array[basename($file)] = trans('admin.add');
                    break;
                case 'D':
                    $array[basename($file)] = trans('admin.delete');
                    break;
                case 'M':
                    $array[basename($file)] = trans('admin.modify');
                    break;
                default:
                    $array[basename($file)] = trans('admin.unknown');
                    break;
            }
        }

        return $array;
    }

    /**
     * Execute commit repository
     *
     * @param ServerModel $server
     * @param string|null $path
     * @return array
     * @throws Exception
     */
    public static function repositoryCommit(ServerModel $server, string|null $path = null): array
    {
        // try parse as git repository
        try {
            // try read remote url
            $url = self::execute($server, ["git", "config", "--get", "remote.origin.url"], $path);

            // connect by http
            if (str_starts_with($url, "http")) {
                $msg = "Git repository not connected by SSH: $url";
                return ['code' => 1, 'msg' => $msg];
            }

            try {
                // git use ssh, auth by public key, but without key passphrase
                // self::execute($server, ["git", "stash", "--include-untracked"], $path);
                // self::execute($server, ["git", "stash", "pop", "--quiet"], $path);
                // self::execute($server, ["git", "checkout", "--ours", "."], $path);
                self::execute($server, ["git", "add", "."], $path);
                self::execute($server, ["git", "commit", "--message=Add Configure Data"], $path);
                $branch = self::execute($server, ["git", "branch", "--show-current"]);
                self::execute($server, ["git", "pull", "origin", trim($branch), "--rebase"], $path);
                self::execute($server, ["git", "push", "origin", trim($branch), "--force-with-lease"], $path);
            } catch (ProcessFailedException $exception) {
                $msg = str_replace("\n", "<br>", $exception->getProcess()->getOutput());
                return ['code' => $exception->getCode(), 'msg' => $msg];
            } catch (Exception $exception) {
                $msg = str_replace("\n", "<br>", $exception->getMessage());
                return ['code' => $exception->getCode(), 'msg' => $msg];
            }
        } catch (Exception) {
            // suppress error when dir not a git repository
        }

        // try parse as svn repository
        try {
            // try read remote url
            $url = self::execute($server, ["svn", "info", "--show-item", "repos-root-url"], $path);

            // connect by svn or http
            if (str_starts_with($url, "svn://") || str_starts_with($url, "http")) {
                $msg = "SVN repository not connected by SSH: $url";
                return ['code' => 1, 'msg' => $msg];
            }

            try {
                // svn use ssh, auth by public key, but without key passphrase
                // self::execute($server, ["svn", "update", "--non-interactive"], $path);
                // self::execute($server, ["svn", "resolve", "--accept", "mine-full", "*", "--force"], $path);
                self::execute($server, ["svn", "add", ".", "--no-ignore", "--force"], $path);
                self::execute($server, ["svn", "commit", "--message", "Add Configure Data"], $path);
            } catch (ProcessFailedException $exception) {
                $msg = str_replace("\n", "<br>", $exception->getProcess()->getOutput());
                return ['code' => $exception->getCode(), 'msg' => $msg];
            } catch (Exception $exception) {
                $msg = str_replace("\n", "<br>", $exception->getMessage());
                return ['code' => $exception->getCode(), 'msg' => $msg];
            }
        } catch (Exception) {
            // suppress error when dir not a svn repository
        }

        return ['code' => 0, 'msg' => ''];
    }

    /**
     * Execute maker command
     *
     * @param ServerModel $server
     * @param array $command
     * @param string|null $path
     * @param string $output
     * @return array
     * @throws Exception
     */
    public static function executeMakerScript(ServerModel $server, array $command, string|null $path = null, string $output = 'stdout'): array
    {
        $script = array_merge(['script/shell/maker.sh'], $command);
        return self::execute($server, $script, $path, $output);
    }

    /**
     * Execute run command
     *
     * @param ServerModel $server
     * @param array $command
     * @param string|null $path
     * @param string $output
     * @return array
     * @throws Exception
     */
    public static function executeRunScript(ServerModel $server, array $command, string|null $path = null, string $output = 'stdout'): array
    {
        $script = array_merge(['script/shell/run.sh'], $command);
        return self::execute($server, $script, $path, $output);
    }

    /**
     * Execute command
     *
     * @param ServerModel $server
     * @param array $command
     * @param string|null $path
     * @param string $output
     * @return array
     * @throws Exception
     */
    public static function execute(ServerModel $server, array $command, string|null $path = null, string $output = 'stdout'): array
    {
        // server path as default
        $path = $path ?? $server->server_root;

        // local machine
        if ($server->ssh_host === '') {
            return self::executeLocal($command, $path);
        }

        // remote machine
        $ssh_host = $server->ssh_host;
        $ssh_pass = $server->ssh_pass;

        // concat ssh command
        $command = implode(' ', array_merge(['cd', $path, '&&'], $command));

        // set host and command
        $command = ['-C', $ssh_host, $command];
        return self::executeRemote('ssh', $command, $ssh_pass, $output);
    }

    /**
     * Execute local command
     *
     * @param array $command
     * @param string|null $path
     * @param string $output
     * @return array
     * @throws Exception
     */
    public static function executeLocal(array $command, string|null $path = null, string $output = 'stdout'): array
    {
        // default timeout 10 seconds
        $process = new Process($command, $path, ['PATH' => getenv('PATH')], null, env('PROCESS_TIMEOUT', 10));
        return self::runProcess($process, $output);
    }

    /**
     * Execute remote command
     *
     * @param array $command
     * @param string $ssh_pass
     * @param string $program
     * @param string $output
     * @return array
     * @throws Exception
     */
    public static function executeRemote(string $program, array $command = [], string $ssh_pass = '', string $output = 'stdout'): array
    {
        // ssh pass script
        if(!file_exists(base_path('vendor/bin/sshpass'))) {
            Artisan::call('sshpass:install');
        }

        if(!strlen($ssh_pass)) {
            throw new Exception('SSH Password Required', -1);
        }

        $pass = [base_path('vendor/bin/sshpass'), $ssh_pass];
        $ssh = [$program, '-o', 'LogLevel=error', '-o', 'StrictHostKeyChecking=no', '-o', 'UserKnownHostsFile=/dev/null'];
        $process = new Process(array_merge($pass, $ssh, $command), null, null, null, env('PROCESS_TIMEOUT', 10));
        return self::runProcess($process, $output);
    }

    /**
     * Execute process
     *
     * @param Process $process
     * @param string $output
     * @return array
     * @throws Exception
     */
    private static function runProcess(Process $process, string $output = 'stdout'): array
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
        if ($output == 'stdout') {
            $msg = $process->getOutput();
            return ['code' => 0, 'msg' => $msg];
        } else if ($output == 'stderr') {
            $msg = $process->getErrorOutput();
            return ['code' => $process->getExitCode(), 'msg' => $msg];
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
        $file = getenv('HOME') . '/.ssh/config';
        if (!file_exists($file)) {
            return [];
        }
        $host = '';
        $config = [];
        $data = explode("\n", file_get_contents($file));
        foreach ($data as $line) {
            $pos = strpos($line, '#');
            $line = trim(substr($line, 0, is_bool($pos) ? strlen($line) : $pos));
            // blank
            if ($line === '') continue;
            // config
            if (is_bool(preg_match("/(\w+)(\s*=\s*|\s+)(.+)/", $line, $matches))) {
                throw new Exception('Invalid Config File Syntax');
            }
            [, $key, , $value] = $matches;
            if ($key === 'Host') {
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
}
