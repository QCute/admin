<?php

namespace App\Admin\Forms\ServerManageForms;

use App\Admin\Controllers\SwitchServerController;
use App\Http\Controllers\ServerListController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @param Request $request
     * @return  RedirectResponse
     * @throws Exception
     */
    public function handle(Request $request): RedirectResponse
    {
        $server = $request->input("server", "");
        $password = $request->input("password", "");
        $channel = $request->input("channel", "");
        $name = $request->input("name", "");
        $tab = $request->input("tab", "");
        $center = trim($request->input("center", ""));
        $world = trim($request->input("world", ""));
        $recommend = $request->input("recommend", "");
        if(!empty($dst) && !SwitchServerController::getServer($channel, $center)) {
            admin_error(trans("admin.invalid_center"));
            return back();
        }
        if(!empty($dst) && !SwitchServerController::getServer($channel, $world)) {
            admin_error(trans("admin.invalid_world"));
            return back();
        }
        // next server id
        $server_id = DB::table("server_list")->where("server_type", "local")->max("server_id") + 1;
        // next port
        $port = DB::table("server_list")->where("server_type", "local")->max("server_port") + 1;
        // node
        $node = basename(env("SERVER_PATH", "server")) . "_" . $server_id;
        // machine
        $config = SwitchServerController::getSSHConfig()[$server];
        $config->Password = $password;
        // long time task
        SwitchServerController::executeMakerScript(["open_server", $name], null, $config);
        // todo fill server list data
        DB::insert("INSERT INTO `server_list` (`server_node`, `server_name`, `server_port`, `server_id`, `server_type`, `open_time`, `tab_name`, `state`, `recommend`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [$node, $name, $port, $server_id, 'local', time(), $tab,  1, $recommend]);
        // success tips
        admin_success(trans("admin.completed"));
        return back();
    }

    /**
     * Build a form here.
     *
     * @throws Exception
     */
    public function form()
    {
        $this->title = trans("admin.open_server");
        $options = [];
        $data = SwitchServerController::getSSHConfig();
        foreach ($data as $item) {
            $options[$item->Host] = $item->Host;
        }
        $this
            ->select("server", trans("admin.server"))
            ->options($options)
            ->required();
        $this
            ->password("password", trans("admin.password"))
            ->required();
        $options = [];
        $list = DB::table("server_list")
            ->where("server_type", "local")
            ->groupBy("channel")
            ->select([
                "channel", 
                "channel_name"
            ])
            ->get();
        foreach ($list as $item) {
            $options[$item->channel] = $item->channel_name;
        }
        $this
            ->select("channel", trans("admin.channel"))
            ->options($options)
            ->required();
        $this
            ->text("name", trans("admin.name"))
            ->required();
        $this
            ->text("tab", trans("admin.tab"))
            ->value("")
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
            ->value("")
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
            ->value("")
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
        // scroll to top
        $this->html("<script>document.querySelector('#pjax-container').scroll(0, 0);</script>");
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
