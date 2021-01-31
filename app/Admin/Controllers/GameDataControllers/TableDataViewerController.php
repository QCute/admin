<?php

namespace App\Admin\Controllers\GameDataControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Admin\Models\GameDataModels\TableDataModel;
use App\Admin\Controllers\SwitchServerController;

class TableDataViewerController extends AdminController
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
        $database = SwitchServerController::getCurrentServer();
        $table = request()->input("table", "");
        $grid = new Grid(new TableDataModel( $database . "." . $table));
        // $table = $grid->model()->getTable();
        // data
        // $array = DB::SELECT("SELECT `COLUMN_NAME`, `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . $database . "' AND `TABLE_NAME` = '{$table}'");
        $data = DB::table("information_schema.COLUMNS")->where("TABLE_SCHEMA", $database)->where("TABLE_NAME", $table)->get();
        foreach ($data as $row) {
            $grid->column($row->COLUMN_NAME, $row->COLUMN_COMMENT)->style("min-width:8em");
        }

        // filter
        $grid->filter(function($filter) use ($database, $table) {
            // remove default id filter
            $filter->disableIdFilter();

            // filter
            // $array = DB::select("SELECT `COLUMN_NAME`, `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . $database . "' AND `TABLE_NAME` = '{$table}' AND `COLUMN_KEY` IN ('PRI', 'UNI', 'MUL')");
            $data = DB::table("information_schema.COLUMNS")->where("TABLE_SCHEMA", $database)->where("TABLE_NAME", $table)->whereIn("COLUMN_KEY", ['PRI', 'UNI', 'MUL'])->get();
            foreach ($data as $row) {
                $filter->like($row->COLUMN_NAME, $row->COLUMN_COMMENT);
            }

        });

        // actions
        $grid->actions(function ($actions) {
            // remove edit
            $actions->disableEdit();

            // remove view
            $actions->disableView();

            // remove delete
            $actions->disableDelete();

        });
        // no action
        $grid->disableActions();
        // no create
        $grid->disableCreateButton(true);
        $grid->disableBatchActions(true);
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return new Form(new TableDataModel());
    }
}
