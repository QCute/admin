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

        //server_node
        //server_name
        //server_host
        //server_ip
        //server_port
        //server_id
        //server_type
        //open_time
        //tab_name
        //center_node
        //center_name
        //center_host
        //center_ip
        //center_port
        //center_id
        //world
        //state
        //recommend
        return $form;
    }

    /**
     * Inner Tool
     */
//    public function publish(Content $content)
//    {
//        SwitchServerController::publishServerList();
//        return $this->index($content->withSuccess(trans("admin.succeeded")));
//    }
//
//    public function sendRequest(Content $content)
//    {
//        // send command
//        $server = request()->input("server", "");
//        if (empty($server)) return $content;
//        // request
//        $array = SwitchServerController::send($server, request()->input("command", ""), json_encode([]));
//        // handle result
//        $ok = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["ok"]), $array["ok"]));
//        $error = implode("", array_map(function ($k, $v) { return "{$k}: {$v}<br/>"; }, array_keys($array["error"]), $array["error"]));
//        // toast tips
//        if (!empty($ok))
//            return $content->withSuccess(trans("admin.succeeded"), $ok);
//        else if (!empty($error))
//            return $content->withError(trans("admin.failed"), $error);
//        else
//            return $content;
//    }
}
