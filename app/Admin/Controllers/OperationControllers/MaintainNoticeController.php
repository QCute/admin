<?php

namespace App\Admin\Controllers\OperationControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Admin\Models\OperationModels\MaintainNoticeModel;

class MaintainNoticeController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(): Grid
    {
        $grid = new Grid(new MaintainNoticeModel());
        $table = $grid->model()->getTable();
        // data
        $array = DB::table("information_schema.COLUMNS")
            ->where("TABLE_SCHEMA", env("DB_DATABASE"))
            ->where("TABLE_NAME", $table)
            ->get();
        foreach ($array as $row) {
            $grid->column($row->COLUMN_NAME, $row->COLUMN_COMMENT);
        }

        // filter
        $grid->filter(function($filter) use ($table) {
            // remove default id filter
            $filter->disableIdFilter();

            // filter
            $array = DB::table("information_schema.COLUMNS")
                ->where("TABLE_SCHEMA", env("DB_DATABASE"))
                ->where("TABLE_NAME", $table)
                ->whereIn("COLUMN_KEY", ['PRI', 'UNI', 'MUL'])
                ->get();
            foreach ($array as $row) {
                $filter->like($row->COLUMN_NAME, $row->COLUMN_COMMENT);
            }

        });

        // actions
        $grid->actions(function ($actions) {
            // remove edit
            // $actions->disableEdit();

            // remove view
            // $actions->disableView();
        });

        // no create
        // $grid->disableCreateButton(true);
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        $form = new Form(new MaintainNoticeModel());
        $form->text('platform', trans("admin.platform"))->required();
        $form->textarea('title', trans("admin.title"))->required();
        $form->textarea('content', trans("admin.content"))->required();
        $form->datetime('start_time', trans("admin.start_time"))->required();
        $form->datetime('end_time', trans("admin.end_time"))->required();
        $form->saving(function (Form $form) {});
        return $form;
    }
}
