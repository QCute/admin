<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use App\Admin\Models\ServerManageModels\MergeServerModel;
use App\Admin\Controllers\SwitchServerController;

class MergeServerController extends AdminController
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
        $form = new Form(new MergeServerModel());
        $form->setTitle(trans("admin.open_server"));
        $form->text("name", trans("admin.name"));
        $form->text("tab", trans("admin.tab"));
        // center
        $options = [];
        $list = DB::table("server_list")
            ->where("server_type", "local")
            ->get();
        foreach ($list as $item) {
            $options[$item->server_node] = $item->server_name;
        }
        // src
        $form->select("src", trans("admin.center_name"))->options($options);
        // dst
        $form->select("dst", trans("admin.world_name"))->options($options);
        // mode
        $form
            ->radio("mode", trans("admin.merge_mode"))
            ->options(["keep" => trans("admin.merge_mode_keep"), "merge" => trans("admin.merge_mode_merge")])
            ->default("keep");
        $form->hidden("action")->value("merge");

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
     * Index interface.
     *
     * @param Content $content
     * @param string $action;
     *
     * @return Content
     */
    public function action(Content $content, string $action): Content
    {
        $src = request()->input("src", "");
        $dst = request()->input("dst", "");
        $mode = request()->input("mode", "");
        if($src == $dst) {
            return $this->displayIndex($content)->withError(trans("admin.merge_same_server"));
        }
        // check server valid
        if(empty($src) || !SwitchServerController::hasServer($src)) {
            return $this->displayIndex($content)->withError(trans("admin.no_src_server"));
        }
        if(empty($dst) || !SwitchServerController::hasServer($dst)) {
            return $this->displayIndex($content)->withError(trans("admin.no_dst_server"));
        }
        // long time task
        $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "merge_server", $src, $dst]);
        $process->run();
        if (!$process->isSuccessful()) {
            return $this->displayIndex($content)->withError($process->getErrorOutput());
        }
        // default
        if ($mode == "merge") {
            // delete entrance
            DB::delete("DELETE FROM `server_list` WHERE `server_node` = ?", [$src]);
        } else {
            // update entrance
            DB::update("UPDATE `server_list` SET `server_id` = (SELECT `server_id` FROM `server_list` WHERE server_node = ?), `server_port` = (SELECT `server_port` FROM `server_list` WHERE server_node = ?) where server_node = ?", [$dst, $dst, $src]);
        }
        // republic server list
        SwitchServerController::publishServerList();
        // success tips
        return $this->displayIndex($content)->withSuccess(trans("admin.completed"));
    }
}
