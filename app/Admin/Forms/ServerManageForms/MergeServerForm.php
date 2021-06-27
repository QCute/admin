<?php

namespace App\Admin\Forms\ServerManageForms;

use Symfony\Component\Process\Process;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use App\Admin\Controllers\SwitchServerController;

class MergeServerForm extends Form {
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
        $src = $request->input("src", "");
        $dst = $request->input("dst", "");
        $mode = $request->input("mode", "");
        if($src == $dst) {
            admin_error(trans("admin.merge_same_server"));
            return back();
        }
        // check server valid
        if(empty($src) || !SwitchServerController::hasServer($src)) {
            admin_error(trans("admin.no_src_server"));
            return back();
        }
        if(empty($dst) || !SwitchServerController::hasServer($dst)) {
            admin_error(trans("admin.no_dst_server"));
            return back();
        }
        // long time task
        $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "merge_server", $src, $dst]);
        $process->run();
        if (!$process->isSuccessful() || !empty($process->getErrorOutput())) {
            admin_error($process->getErrorOutput());
            return back();
        }
        // default
        if ($mode == "merge") {
            // delete entrance
            DB::table("server_list")
                ->where("server_node", "=", $src)
                ->delete();
        } else {
            // update entrance
            $sql = "
                UPDATE 
                    `server_list` 
                SET 
                    `server_id` = ( SELECT `server_id` FROM `server_list` WHERE `server_node = ? ), 
                    `server_port` = ( SELECT `server_port` FROM `server_list` WHERE `server_node` = ? ) 
                WHERE `server_node` = ?
            ";
            DB::update($sql, [$dst, $dst, $src]);
        }
        // republic server list
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
            ->where("server_type", "local")
            ->get();
        foreach ($list as $item) {
            $options[$item->server_node] = $item->server_name;
        }
        // src
        $this
            ->select("src", trans("admin.center_name"))
            ->options($options)
            ->required();
        // dst
        $this
            ->select("dst", trans("admin.world_name"))
            ->options($options)
            ->required();
        // mode
        $this
            ->radio("mode", trans("admin.merge_mode"))
            ->options([
                "keep" => trans("admin.merge_mode_keep"),
                "merge" => trans("admin.merge_mode_merge"),
            ])
            ->default("keep")
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
