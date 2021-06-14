<?php

namespace App\Admin\Forms\ServerManageForms;

use Symfony\Component\Process\Process;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use App\Admin\Controllers\SwitchServerController;

class OpenServerForm extends Form {
    use DefaultDatetimeFormat;
    
    /**
     * The form title.
     *
     * @var  string
     */
    public $title = '';

    /**
     * Handle the form request.
     *
     * @param  Request $request
     *
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $name = $request->input("name", "");
        $tab = $request->input("tab", "");
        $center = trim($request->input("center", ""));
        $world = trim($request->input("world", ""));
        $recommend = $request->input("recommend", "");
        if(!empty($dst) && !SwitchServerController::hasServer($center)) {
            admin_error(trans("admin.invalid_center"));
            return back();
        }
        if(!empty($dst) && !SwitchServerController::hasServer($world)) {
            admin_error(trans("admin.invalid_world"));
            return back();
        }
        // node
        $server_id = SwitchServerController::nextServerId("local");
        $port = SwitchServerController::nextServerPort("local");
        $node = basename(env("SERVER_PATH", "server")) . "_" . $server_id;
        // long time task
        $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "open_server", $name]);
        $process->run();
        if (!$process->isSuccessful()) {
            admin_error($process->getErrorOutput());
            return back();
        }
        // todo fill server list data
        DB::insert("INSERT INTO `server_list` (`server_node`, `server_name`, `server_port`, `server_id`, `server_type`, `open_time`, `tab_name`, `state`, `recommend`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [$node, $name, $port, $server_id, 'local', time(), $tab,  1, $recommend]);
        // update server list
        SwitchServerController::publishServerList();
        // success tips
        admin_success(trans("admin.completed"));
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->title = trans("admin.open_server");
        $this
            ->text("name", trans("admin.name"))
            ->required();
        $this
            ->text("tab", trans("admin.tab"))
            ->required();
        // center
        $options = [];
        $list = DB::table("server_list")
            ->where("server_type", "center")
            ->get();
        foreach ($list as $item) {
            $options[$item->server_node] = $item->server_name;
        }
        $this
            ->select("center", trans("admin.center_name"))
            ->options($options)
            ->required();
        // world
        $options = [];
        $list = DB::table("server_list")
            ->where("server_type", "world")
            ->get();
        foreach ($list as $item) {
            $options[$item->server_node] = $item->server_name;
        }
        $this
            ->select("world", trans("admin.world_name"))
            ->options($options)
            ->required();
        // status
        $options = [
            "new" => trans("admin.server_recommend.new"),
            "hot" => trans("admin.server_recommend.hot"),
            "recommend" => trans("admin.server_recommend.recommend"),
        ];
        $this
            ->select("recommend", trans("admin.server_recommend.recommend"))
            ->options($options)
            ->required();
    }

    /**
     * The data of the form.
     *
     * @return  array $data
     */
    public function data(): array
    {
        return [];
    }
}
