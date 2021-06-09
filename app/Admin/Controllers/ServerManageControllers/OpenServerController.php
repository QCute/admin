<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\DB;
use App\Admin\Models\ServerManageModels\OpenServerModel;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;
use Encore\Admin\Controllers\AdminController;
use App\Admin\Controllers\SwitchServerController;

class OpenServerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content): Content
    {
        $action = request()->input("action", "");
        if (!empty($action)) {
            return $this->action($content, $action);
        }
        return $this->displayIndex($content);
    }

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function displayIndex(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description($this->description['create'] ?? trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form(): Form
    {
        $form = new Form(new OpenServerModel());
        $form->setTitle(trans("admin.open_server"));
        $form->text("name", trans("admin.name"));
        $form->text("tab", trans("admin.tab"));
        // center
        $options = [];
        $list = DB::table("server_list")
            ->where("server_type", "center")
            ->get();
        foreach ($list as $item) {
            $options[$item->server_node] = $item->server_name;
        }
        $form->select("center", trans("admin.center_name"))->options($options);
        // world
        $options = [];
        $list = DB::table("server_list")
            ->where("server_type", "world")
            ->get();
        foreach ($list as $item) {
            $options[$item->server_node] = $item->server_name;
        }
        $form->select("world", trans("admin.world_name"))->options($options);
        // status
        $options = [
            "new" => trans("admin.server_recommend.new"),
            "hot" => trans("admin.server_recommend.hot"),
            "recommend" => trans("admin.server_recommend.recommend"),
        ];
        $form->select("recommend", trans("admin.server_recommend.recommend"))->options($options);
        $form->hidden("action")->value("open");

        $form->disableViewCheck();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();

        $form->tools(function (Form\Tools $tools) {
            // remove list
            $tools->disableList();
            // remove delete
            $tools->disableDelete();
            // remove view
            $tools->disableView();
        });

        $form->setAction(request()->path());

        return $form;
    }

    /**
     * Make a form builder.
     *
     * @param Content $content
     * @param string $action
     *
     * @return Content
     */
    public function action(Content $content, string $action): Content
    {
        $name = request()->input("name", "");
        $tab = request()->input("tab", "");
        $center = trim(request()->input("center", ""));
        $world = trim(request()->input("world", ""));
        $recommend = request()->input("recommend", "");
        // $isConnectWorld = request()->input('is_connect_world');
        if(empty($name)) {
            return $this->displayIndex($content)->withError(trans("admin.empty_name"));
        }
        if(!empty($dst) && !SwitchServerController::hasServer($center)) {
            return $this->displayIndex($content)->withError(trans("admin.invalid_center"));
        }
        if(!empty($dst) && !SwitchServerController::hasServer($world)) {
            return $this->displayIndex($content)->withError(trans("admin.invalid_world"));
        }
        // node
        $server_id = SwitchServerController::nextServerId("local");
        $port = SwitchServerController::nextServerPort("local");
        $node = basename(env("SERVER_PATH", "server")) . "_" . $server_id;
        // long time task
        $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "open_server", $name]);
        $process->run();
        if (!$process->isSuccessful()) {
            return $this->displayIndex($content)->withError($process->getErrorOutput());
        }
        // todo fill server list data
        DB::insert("INSERT INTO `server_list` (`server_node`, `server_name`, `server_port`, `server_id`, `server_type`, `open_time`, `tab_name`, `state`, `recommend`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [$node, $name, $port, $server_id, 'local', time(), $tab,  1, $recommend]);
        // update server list
        SwitchServerController::publishServerList();
        // success tips
        return $this->displayIndex($content)->withSuccess(trans("admin.completed"));
    }
}
