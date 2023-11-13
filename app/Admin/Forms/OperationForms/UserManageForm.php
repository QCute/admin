<?php

namespace App\Admin\Forms\OperationForms;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserManageForm extends Form {
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
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        // send command
        $server = $request->input("server", "");
        $command = $request->input($request->input("command", ""), "");
        $role_id = $request->input("role_id", "");
        $role_id = array_map(function($row) { return (int)$row; }, explode(",", str_replace("\n", ",", $role_id)));
        // request
        $array = SwitchServerController::send($server, $command, ["role_id" => $role_id]);
        SwitchServerController::handleSendResult($array);
        // back
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->title = trans("admin.manage");
        $options = [
            "current" => trans("admin.current_server"),
            "channel" => trans("admin.current_channel"),
            "all" => trans("admin.all_server"),
        ];
        $this
            ->select("server", trans("admin.server"))
            ->options($options)
            ->required();
        $help = trans("admin.one_per_line") . " " . trans("or") . " " . trans("admin.split_with_comma");
        $this
            ->textarea("role_id", trans("admin.role_id"))
            ->style("resize", "vertical")
            ->help($help)
            ->required();
        $this
            ->radioButton("command", trans("admin.type"))
            ->options([
                "account" => trans("admin.account"),
                "chat" => trans("admin.chat"),
            ])
            ->when("account", function (Form $form) {
                $form
                    ->radio("account", trans("admin.account"))
                    ->options([
                        "set_role_normal" => trans("admin.set_role_normal"),
                        "set_role_insider" => trans("admin.set_role_insider"),
                        "set_role_master" => trans("admin.set_role_master"),
                        "set_role_refuse" => trans("admin.set_role_refuse"),
                    ])
                    ->default("set_role_normal")
                    ->required();
            })
            ->when("chat", function (Form $form) {
                $form
                    ->radio("chat", trans("admin.chat"))
                    ->options([
                        "set_role_chat_unlimited" => trans("admin.set_unlimited"),
                        "set_role_chat_silent" => trans("admin.set_silent"),
                        "set_role_chat_silent_world" => trans("admin.set_silent_world"),
                        "set_role_chat_silent_guild" => trans("admin.set_silent_guild"),
                        "set_role_chat_silent_scene" => trans("admin.set_silent_scene"),
                        "set_role_chat_silent_private" => trans("admin.set_silent_private"),
                    ])
                    ->default("set_role_chat_unlimited")
                    ->required();
            })
            ->default("account")
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
