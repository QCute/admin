<?php

namespace App\Admin\Controllers\GameDataControllers;

use App\Admin\Controllers\SwitchServerController;
use App\Admin\Models\GameDataModels\TableDataModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Support\Facades\DB;

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
    protected function grid(): Grid
    {
        $connection = SwitchServerController::getConnection();
        $database = SwitchServerController::getCurrentServer();
        $table = request()->input("table", "");
        $key = DB::connection($connection)
            ->table("information_schema.COLUMNS")
            ->select(["COLUMN_NAME"])
            ->where("TABLE_SCHEMA", $database)
            ->where("TABLE_NAME", $table)
            ->where("COLUMN_KEY", "PRI")
            ->get()
            ->first()
            ->COLUMN_NAME;
        $grid = new Grid(new TableDataModel($connection, $table, $key));
        $grid->paginate(env("ADMIN_PER_PAGE", 20));
        // data
        $data = DB::connection($connection)
            ->table("information_schema.COLUMNS")
            ->select(["COLUMN_NAME", "COLUMN_COMMENT"])
            ->where("TABLE_SCHEMA", $database)
            ->where("TABLE_NAME", $table)
            ->orderBy('ORDINAL_POSITION')
            ->get()
            ->toArray();

        foreach ($data as $row) {
            $grid
                ->column($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                ->style("min-width:8em")
                ->sortable();
        }

        // filter
        $grid->filter(function($filter) use ($connection, $database, $table) {
            // remove default id filter
            $filter->disableIdFilter();

            // filter
            $data = DB::connection($connection)
                ->table("information_schema.COLUMNS")
                ->select(["COLUMN_NAME", "COLUMN_COMMENT"])
                ->where("TABLE_SCHEMA", $database)
                ->where("TABLE_NAME", $table)
                ->whereIn("COLUMN_KEY", ['PRI', 'UNI', 'MUL'])
                ->get();
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
        // no batch
        $grid->disableBatchActions(true);
        // export
        $grid->export(function ($export) use ($table) {
            $export->filename($table);
        });
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        return new Form(new TableDataModel());
    }
}
