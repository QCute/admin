<?php

namespace App\Admin\Forms\OperationForms;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GameNoticeForm extends Form {
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
        // send notice
        $server = $request->input("server", "");
        // construct data
        $data = [
            "title" => $request->input("title", ""),
            "content" => $request->input("content", ""),
            "items" => $request->input("items", "") ?? "",
        ];
        // request
        $array = SwitchServerController::send($server, "notice", $data);
        // handle result
        $ok = implode("", array_map(function ($k, $v) {
            return "$k: $v<br/>";
        }, array_keys($array["ok"]), $array["ok"]));
        $error = implode("", array_map(function ($k, $v) {
            return "$k: $v<br/>";
        }, array_keys($array["error"]), $array["error"]));
        // tips
        if (!empty($ok)) {
            admin_success(trans("admin.succeeded"), $ok);
        }
        if (!empty($error)) {
            admin_error(trans("admin.failed"), $error);
        }
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->title = trans("admin.notice");
        $options = [
            "current" => trans("admin.current_server"),
            "all" => trans("admin.all_server"),
        ];
        $this
            ->select("server", trans("admin.server"))
            ->options($options)
            ->required();
        $options = [
            "board" => "公告栏",
            "mail" => "邮件",
            "chat" => "聊天框",
            "float" => "漂浮提示",
            "scroll" => "滚动提示",
            "pop" => "弹出",
            "dialog" => "弹出框",
        ];
        $this
            ->select("type", trans("admin.type"))
            ->options($options)
            ->required();
        $this
            ->text("title", trans("admin.title"))
            ->required();
        $this
            ->textarea("content", trans("admin.content"))
            ->style("resize", "vertical")
            ->required();

        $help = "<a href='configure-assistant' target='_blank'>" . trans("admin.configure_assistant") . "</a>";
        $this
            ->textarea("items", trans("admin.items"))
            ->style("resize", "vertical")
            ->help($help)
            ->default("")
            ->value("");
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
