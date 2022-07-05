<?php

namespace App\Admin\Controllers\GameDataControllers;

use App\Admin\Controllers\SwitchServerController;
use App\Admin\Models\GameDataModels\TableDataModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Exception;

class TableDataListController extends AdminController
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
     * @throws Exception
     */
    protected function grid(): Grid
    {
        $path = request()->path();
        $connection = SwitchServerController::getConnection();
        $database = SwitchServerController::getCurrentServer();
        $grid = new Grid(new TableDataModel($connection, "information_schema.TABLES", "TABLE_NAME"));
        $grid->paginate(env("ADMIN_PER_PAGE", 20));
        $grid->model()->where("TABLE_SCHEMA", "=", $database);
        if (is_int(strpos($path, "user"))) {
            $grid
                ->model()
                ->where("TABLE_NAME", "NOT LIKE", "%_data")
                ->where("TABLE_NAME", "NOT LIKE", "%_log");
        } else if (is_int(strpos($path, "configure"))) {
            $grid
                ->model()
                ->where("TABLE_NAME", "LIKE", "%_data");
        } else if (is_int(strpos($path, "log"))) {
            $grid
                ->model()
                ->where("TABLE_NAME", "LIKE", "%_log");
        } else {
            throw new Exception("Unknown Path: $path");
        }
        $data = [
            (object)[
                "OPERATION" => false,
                "NAME" => "TABLE_NAME",
                "COMMENT" => trans("admin.table"),
            ],
            (object)[
                "OPERATION" => false,
                "NAME" => "TABLE_COMMENT",
                "COMMENT" => trans("admin.name"),
            ],
            (object)[
                "OPERATION" => true,
                "NAME" => "OPERATION",
                "COMMENT" => trans("admin.operation"),
            ],
        ];
        foreach ($data as $row) {
            $grid
                ->column($row->NAME, $row->COMMENT)
                ->style("min-width:8em")
                ->display(function () use ($row) {
                    if ($row->OPERATION) {
                        $href = "table-data-viewer?table=$this->TABLE_NAME";
                        return "<a href='$href'>" . trans("admin.view"). "</a>";
                    } else {
                        return $this->{$row->NAME};
                    }
                });
        }

        // filter
        $grid->filter(function($filter) use ($data) {
            // remove default id filter
            $filter->disableIdFilter();

            // filter
            foreach ($data as $row) {
                if ($row->OPERATION) continue;
                $filter->like($row->NAME, $row->COMMENT);
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
        $grid->export(function ($export) use ($path) {
            $export->filename(str_replace("-", "_", $path));
            $export->except(["OPERATION"]);
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
