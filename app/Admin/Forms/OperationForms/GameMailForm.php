<?php

namespace App\Admin\Forms\OperationForms;

use App\Admin\Controllers\SwitchServerController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GameMailForm extends Form {
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
        $server = $request->input("server", "");
        $role_id = $request->input("role_id", "");
        $role_id = array_map(function($row) { return (int)$row; }, explode(",", str_replace("\n", ",", $role_id)));
        // construct data
        $data = [
            "title" => $request->input("title", ""),
            "content" => $request->input("content", ""),
            "items" => $request->input("items", "") ?? "",
            "role_id" => $role_id,
        ];
        // request
        $array = SwitchServerController::send($server, "mail", $data);
        SwitchServerController::handleSendResult($array);
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->title = trans("admin.mail");
        $options = [
            "current" => trans("admin.current_server"),
            "channel" => trans("admin.current_channel"),
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
        $help = "<a href='/assistant/configure-assistant' target='_blank'>" . trans("admin.configure_assistant") . "</a>";
        $this
            ->textarea("items", trans("admin.items"))
            ->style("resize", "vertical")
            ->help($help)
            ->default("")
            ->value("");
        $help = trans("admin.one_per_line") . " " . trans("or") . " " . trans("admin.split_with_comma");
        $this
            ->textarea("role_id", trans("admin.role_id"))
            ->style("resize", "vertical")
            ->help($help)
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
