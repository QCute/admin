<?php

namespace App\Admin\Controllers\GameDataControllers;

use App\Admin\Models\GameDataModels\ClientErrorLogModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Support\Facades\DB;

class ClientErrorLogController extends AdminController
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
        $grid = new Grid(new ClientErrorLogModel());
        $grid->paginate(env("ADMIN_PER_PAGE", 20));
        $table = $grid->model()->getTable();
        // data
        $data = DB::table("information_schema.COLUMNS")
            ->where("TABLE_SCHEMA", env("DB_DATABASE"))
            ->where("TABLE_NAME", $table)
            ->get();
        foreach ($data as $row) {
            $grid
                ->column($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                ->style("min-width:8em");
        }

        // filter
        $grid->filter(function($filter) use ($table) {
            // remove default id filter
            $filter->disableIdFilter();

            // filter
            $data = DB::table("information_schema.COLUMNS")
                ->where("TABLE_SCHEMA", env("DB_DATABASE"))
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
    protected function form(): Form
    {
        return new Form(new ClientErrorLogModel());
    }
}
