<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Admin\Models\ServerManageModels\ServerListModel;

class ServerListManageController extends AdminController
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
        $grid = new Grid(new ServerListModel());
        $data = DB::table("information_schema.COLUMNS")
            ->where("TABLE_SCHEMA", env("DB_DATABASE"))
            ->where("TABLE_NAME", "server_list")
            ->get();
        foreach ($data as $row) {
            $grid
                ->column($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                ->style("min-width:8em");
        }
        // filter
        $grid->filter(function($filter) {
            // remove default id filter
            $filter->disableIdFilter();

            $data = DB::table("information_schema.COLUMNS")
                ->where("TABLE_SCHEMA", env("DB_DATABASE"))
                ->where("TABLE_NAME", "server_list")
                ->whereIn("COLUMN_KEY", ['PRI', 'UNI', 'MUL'])
                ->get();
            foreach ($data as $row) {
                $filter->like($row->COLUMN_NAME, $row->COLUMN_COMMENT);
            }

        });

        // actions
        $grid->actions(function ($actions) {
            // remove view
            $actions->disableView();
            // remove delete
            $actions->disableDelete();
        });
        // not batch
        $grid->disableBatchActions(true);
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        $form = new Form(new ServerListModel());
        $data = DB::table("information_schema.COLUMNS")
            ->where("TABLE_SCHEMA", env("DB_DATABASE"))
            ->where("TABLE_NAME", "server_list")
            ->get();
        foreach ($data as $row) {

            if ($row->COLUMN_NAME == "center_name") {
                $options = [];
                $list = DB::table("server_list")
                    ->where("server_type", "center")
                    ->get();
                foreach ($list as $item) {
                    $options[$item->server_node] = $item->server_name;
                }
                $form
                    ->select($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                    ->options($options)
                    ->required();
            } else if ($row->COLUMN_NAME == 'world') {
                $options = [];
                $list = DB::table("server_list")
                    ->where("server_type", "world")
                    ->get();
                foreach ($list as $item) {
                    $options[$item->server_node] = $item->server_name;
                }
                $form
                    ->select($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                    ->options($options)
                    ->required();
            } else if ($row->COLUMN_NAME == 'recommend') {
                $options = [
                    "new" => trans("admin.server_recommend.new"),
                    "hot" => trans("admin.server_recommend.hot"),
                    "recommend" => trans("admin.server_recommend.recommend"),
                ];
                $form
                    ->select($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                    ->options($options)
                    ->required();
            } else {
                $form
                    ->text($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                    ->required();
            }
        }
        return $form;
    }
}
