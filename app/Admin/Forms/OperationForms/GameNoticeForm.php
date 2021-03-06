<?php

namespace App\Admin\Forms\OperationForms;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use App\Admin\Controllers\SwitchServerController;

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
     *
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        // send notice
        $server = $request->input("server", "");
        // construct data
        $json = [
            "title" => $request->input("title"),
            "content" => $request->input("content"),
            "items" => $request->input("items")
        ];
        // request
        $array = SwitchServerController::send($server, "notice", $json);
        // handle result
        $ok = implode("", array_map(function ($k, $v) {
            return "{$k}: {$v}<br/>";
        }, array_keys($array["ok"]), $array["ok"]));
        $error = implode("", array_map(function ($k, $v) {
            return "{$k}: {$v}<br/>";
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
            "this" => trans("admin.current_server"),
            "all" => trans("admin.all_server"),
        ];
        $this
            ->select("server", trans("admin.server"))
            ->options($options)
            ->required();
        $this
            ->text("title", trans("admin.title"))
            ->required();
        $this
            ->textarea("content", trans("admin.content"))
            ->style("resize", "vertical")
            ->required();
        $help = "物品可使用<a href='/configure-assistant' target='_blank'>配表助手</a>生成";
        $this
            ->textarea("items", trans("admin.items"))
            ->style("resize", "vertical")
            ->help($help)
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
