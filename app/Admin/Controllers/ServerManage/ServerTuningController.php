<?php

namespace App\Admin\Controllers\ServerManage;

use App\Admin\Builders\Form;
use App\Admin\Controllers\Controller;
use App\Admin\Services\Auth\AuthService;
use App\Admin\Services\Extend\DatabaseService;
use App\Admin\Services\Extend\MachineService;
use App\Admin\Services\Extend\ServerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServerTuningController extends Controller
{
    public function index(Request $request)
    {
        $server = ServerService::getServer();

        $form = new Form();

        // tuning support check
        if($server->ssh_host === '' && $server->ssh_pass === '') {

            $form->display('')->value('This server does not support tuning');

            return $form->name(trans('admin.form.show'))->build();
        }

        // owner lock check
        $owner = $this->getServerLockOwner($request)['owner'] ?? '';

        if ((is_null($owner) || $owner !== '') && $owner != AuthService::user()->name) {

            $form->display('')->value('This server lock by {$owner}');

            return $form->name(trans('admin.form.show'))->build();
        }

        $form->display('get-local-time')->label(trans('admin.local.time'));

        $form->display('get-server-time')->label(trans('admin.server.time.get'));

        $form->dateTime('set-server-time')->label(trans('admin.server.time.set'));

        $form->display('get-server-open-time')->label(trans('admin.server.time.open.get'));

        $form->dateTime('set-server-open-time')->label(trans('admin.server.time.open.set'));

        $form->display('get-server-state')->label(trans('admin.server.state'));

        $path = dirname($request->path());

        // script
        $form->html('script')->value(<<<HTML
            <script>
                // get local time loop
                function getLocalTime() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = (now.getMonth() + 1).toString(10, 2).padStart(2, '0');
                    const date = now.getDate().toString(10, 2).padStart(2, '0');
                    const hour = now.getHours().toString(10, 2).padStart(2, '0');
                    const minute = now.getMinutes().toString(10, 2).padStart(2, '0');
                    const second = now.getSeconds().toString(10, 2).padStart(2, '0');
                    const time = document.querySelector('[name=get-local-time]');
                    if(time) {
                        time.value = year + '-' + month + '-' + date + ' ' + hour + ':' + minute + ':' + second;
                        setTimeout(() => getLocalTime(), 1000);
                    }
                }

                // get server time loop
                async function getServerTime() {
                    const response = await fetch('/' + '{$path}' + '/get-server-time', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    const data = await response.json();
                    const time = document.querySelector('[name=get-server-time]');
                    if(!time) return;
                    time.value = data.time;
                    setTimeout(() => getServerTime(), 1000);
                }

                // get server open time
                async function getServerOpenTime() {
                    const response = await fetch('/' + '{$path}' + '/get-server-open-time', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    const data = await response.json();
                    const time = document.querySelector('[name=get-server-open-time]');
                    if(!time) return;
                    time.value = data.time;
                }

                // get server state loop
                async function getServerState() {
                    const response = await fetch('/' + '{$path}' + '/get-server-state', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    const data = await response.json();
                    const state = document.querySelector('[name=get-server-state]');
                    if(!state) return;
                    state.value = data.state;
                    setTimeout(() => getServerState(), 1000);
                }

                // time and state loop
                getLocalTime();
                getServerTime();
                getServerOpenTime();
                getServerState();
            </script>
HTML);

        return $form->name(trans('admin.form.show'))->build();
    }

    public function getServerTime()
    {
        $server = ServerService::getServer();
        ['msg' => $timestamp] = MachineService::execute($server, ['date', '+%s']);
        $time = date('Y-m-d H:i:s', intval($timestamp));
        return ['code' => 0, 'msg' => '', 'time' => $time];
    }

    public function setServerTime(Request $request) 
    {
        $time = $request->input('time');
        $server = ServerService::getServer();
        $msg = MachineService::execute($server, ['date', '-s', $time]);
        return ['code' => 0, 'msg' => $msg];
    }

    public function getServerOpenTime()
    {
        $server = ServerService::getServer();
        ['msg' => $openTime] = MachineService::executeMakerScript($server, ['cfg', 'get', $server->node, '"main, open_time"']);
        $time = date('Y-m-d H:i:s', intval($openTime));
        return ['code' => 0, 'msg' => '', 'time' => $time];
    }

    public function setServerOpenTime(Request $request)
    {
        $time = $request->input('time');
        $server = ServerService::getServer();
        $openTime = strtotime($time);
        $msg = MachineService::execute($server, ['cfg', 'set', $server->node, '"main, open_time"', $openTime]);
        return ['code' => 0, 'msg' => $msg];
    }

    public function setServerStart()
    {
        $server = ServerService::getServer();
        $msg = MachineService::execute($server, [$server->node, 'start']);
        return ['code' => 0, 'msg' => $msg];
    }

    public function setServerStop()
    {
        $server = ServerService::getServer();
        $msg = MachineService::execute($server, [$server->node, 'stop']);
        return ['code' => 0, 'msg' => $msg];
    }

    public function getServerState()
    {
        $server = ServerService::getServer();
        ['msg' => $state] = MachineService::executeRunScript($server, [$server->node, 'state']);
        $state = $state === '' ? trans('admin.server.active') : trans('admin.server.down');
        return ['code' => 0, 'msg' => '', 'state' => $state];
    }

    public function setServerTruncate()
    {
        $server = ServerService::getServer();
        $connection = DatabaseService::changeConnection($server);
        $data = $connection
            ->table('information_schema.TABLES')
            ->select('TABLE_NAME')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'NOT LIKE', '%_data')
            ->get()
            ->toArray();

        foreach ($data as $row) {
            $connection->table($row->TABLE_NAME)->truncate();
        }

        return ['code' => 0, 'msg' => ''];
    }

    public function getServerLockOwner(Request $request)
    {
        $server = $request->input('server');
        $server = ServerService::getServer($server);
        if(!$server) {
            return ['code' => 1, 'msg' => 'Server not found', 'owner' => ''];
        }

        $file = storage_path('logs/server_state.json');
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            $owner = $data[$server->id]['owner'] ?? '';
            return ['code' => 0, 'msg' => '', 'owner' => $owner];
        } else {
            return ['code' => 0, 'msg' => '', 'owner' => ''];
        }
    }

    public function setServerLockOwner(Request $request)
    {
        $server = $request->input('server');
        $server = ServerService::getServer($server);
        if(!$server) {
            return ['code' => 1, 'msg' => 'Server not found', 'owner' => ''];
        }
        
        $file = storage_path('logs/server_state.json');
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
        } else {
            $data = [];
        }

        $owner = $request->input('owner');
        $data[] = [$server->id => ['owner' => $owner, 'time' => now()]];
        $data = json_encode($data);
        file_put_contents($file, $data);

        return ['code' => 0, 'msg' => ''];
    }
}
