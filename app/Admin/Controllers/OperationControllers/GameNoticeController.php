<?php

namespace App\Admin\Controllers\OperationControllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use App\Admin\Models\OperationModels\GameNoticeModel;
use App\Admin\Controllers\SwitchServerController;


class GameNoticeController extends AdminController
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
        $form = new Form(new GameNoticeModel());
        $form->setTitle(trans("admin.notice"));
        $options = [
            "this" => trans("admin.current_server"),
            "all" => trans("admin.all_server"),
        ];
        $form->select("server", trans("admin.server"))->options($options);
        $form->text("title", trans("admin.title"));
        $form->textarea("content", trans("admin.content"))->style("resize", "vertical");
        $help = "物品可使用<a href='/configure-assistant' target='_blank'>配表助手</a>生成";
        $form->textarea("items", trans("admin.items"))->style("resize", "vertical")->help($help);
        $form->hidden("action")->value("generate");

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
    public function action(Content $content, string $action)
    {
        // send notice
        $server = request()->input("server", "");
        if (empty($server)) {
            return $this->displayIndex($content)->withError(trans("admin.error"));
        }
        // construct data
        $json = json_encode([
            "title" => request()->input("title"),
            "content" => request()->input("content"),
            "items" => request()->input("items")
        ]);
        // request
        $array = SwitchServerController::send($server, "notice", $json);
        // handle result
        $ok = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["ok"]), $array["ok"]));
        $error = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["error"]), $array["error"]));
        // toast tips
        if (!empty($ok))
            return $content->withSuccess(trans("admin.succeeded"), $ok);
        if (!empty($error))
            return $content->withError(trans("admin.failed"), $error);
        return $this->displayIndex($content);
    }
}
