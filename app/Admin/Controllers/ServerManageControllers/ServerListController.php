<?php

namespace App\Admin\Controllers\ServerManageControllers;

use App\Admin\Controllers\SwitchServerController;
use App\Admin\Models\ServerManageModels\ServerListModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Support\Facades\DB;

class ServerListController extends AdminController
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
        $grid->paginate(env("ADMIN_PER_PAGE", 20));
        $table = $grid->model()->getTable();
        $data = DB::table("information_schema.COLUMNS")
            ->where("TABLE_SCHEMA", env("DB_DATABASE"))
            ->where("TABLE_NAME", $table)
            ->orderBy('ORDINAL_POSITION')
            ->get()
            ->toArray();

        foreach ($data as $row) {
            $grid
                ->column($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                ->style("min-width:8em");
        }
        // filter
        $grid->filter(function($filter) use ($table) {
            // remove default id filter
            $filter->disableIdFilter();

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
            // remove view
            $actions->disableView();
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
        $table = $form->model()->getTable();
        $data = DB::table("information_schema.COLUMNS")
            ->where("TABLE_SCHEMA", env("DB_DATABASE"))
            ->where("TABLE_NAME", $table)
            ->orderBy('ORDINAL_POSITION')
            ->get()
            ->toArray();

        foreach ($data as $row) {

            switch ($row->COLUMN_NAME) {
                case "server_id": {
                    $server_id = SwitchServerController::nextServerId("local");
                    $form
                        ->text($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->value($server_id)
                        ->required();
                    break;
                }
                case "server_type": {
                    $options = [
                        "local" => trans("admin.server_type.local"),
                        "center" => trans("admin.server_type.center"),
                        "world" => trans("admin.server_type.world"),
                    ];
                    $form
                        ->select($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->options($options)
                        ->required();
                    break;
                }
                case "center": {
                    $options = [0 => trans("admin.nothing")];
                    $list = DB::table($table)
                        ->where("server_type", "center")
                        ->get();
                    foreach ($list as $item) {
                        $options[$item->server_id] = $item->server_name;
                    }
                    $form
                        ->select($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->options($options)
                        ->value(0);
                    break;
                }
                case "world": {
                    $options = [0 => trans("admin.nothing")];
                    $list = DB::table($table)
                        ->where("server_type", "world")
                        ->get();
                    foreach ($list as $item) {
                        $options[$item->server_id] = $item->server_name;
                    }
                    $form
                        ->select($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->options($options)
                        ->value(0);
                    break;
                }
                case "open_time": {
                    $form
                        ->text($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->value(strtotime(date("Y-m-d"), time()))
                        ->required();
                    break;
                }
                case "recommend": {
                    $options = [
                        "new" => trans("admin.server_recommend.new"),
                        "hot" => trans("admin.server_recommend.hot"),
                        "recommend" => trans("admin.server_recommend.recommend"),
                    ];
                    $form
                        ->select($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->options($options)
                        ->required();
                    break;
                }
                case "state": {
                    $form
                        ->hidden($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->value("");
                    break;
                }
                case "ssh_pass":
                case "ssh_host":
                case "tab_name": {
                    $form
                        ->text($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->value("");
                    break;
                }
                case "created_at":
                case "updated_at": break;
                default: {
                    $form
                        ->text($row->COLUMN_NAME, $row->COLUMN_COMMENT)
                        ->value("")
                        ->required();
                    break;
                }
            }
        }
        $form->saving(function ($form) {
            $form->state = empty($form->ssh_pass) ? "" : $form->state;
            $form->ssh_pass = empty($form->ssh_pass) ? "" : $form->ssh_pass;
            $form->ssh_host = empty($form->ssh_host) ? "" : $form->ssh_host;
            $form->tab_name = empty($form->tab_name) ? "" : $form->tab_name;
            $form->center = empty($form->center) ? "" : $form->center;
            $form->world = empty($form->world) ? "" : $form->world;
        });
        return $form;
    }
}
