<?php

namespace App\Admin\Controllers\OperationControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Admin\Models\OperationModels\ImpeachModel;

class ImpeachController extends AdminController
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
    protected function grid()
    {
        $grid = new Grid(new ImpeachModel());
        $table = $grid->model()->getTable();
        // data
        // $array = DB::SELECT("SELECT `COLUMN_NAME`, `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . env("DB_DATABASE") . "' AND `TABLE_NAME` = '{$table}'");
        $array = DB::table("information_schema.COLUMNS")->where("TABLE_SCHEMA", env("DB_DATABASE"))->where("TABLE_NAME", $table)->get();
        foreach ($array as $row) {
            $grid->column($row->COLUMN_NAME, $row->COLUMN_COMMENT);
        }

        // filter
        $grid->filter(function($filter) use ($table) {
            // remove default id filter
            $filter->disableIdFilter();

            // filter
            // $array = DB::select("SELECT `COLUMN_NAME`, `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . env("DB_DATABASE") . "' AND `TABLE_NAME` = '{$table}' AND `COLUMN_KEY` IN ('PRI', 'UNI', 'MUL')");
            $array = DB::table("information_schema.COLUMNS")->where("TABLE_SCHEMA", env("DB_DATABASE"))->where("TABLE_NAME", $table)->whereIn("COLUMN_KEY", ['PRI', 'UNI', 'MUL'])->get();
            foreach ($array as $row) {
                $filter->like($row->COLUMN_NAME, $row->COLUMN_COMMENT);
            }

        });

        // actions
        $grid->actions(function ($actions) {
            // remove edit
            $actions->disableEdit();

            // remove view
            $actions->disableView();
        });

        // no create
        $grid->disableCreateButton(true);
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return new Form(new ImpeachModel());
    }
}
