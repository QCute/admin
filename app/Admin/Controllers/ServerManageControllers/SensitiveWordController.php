<?php

namespace App\Admin\Controllers\ServerManageControllers;

use Encore\Admin\Grid\Displayers\ContextMenuActions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Admin\Models\ServerManageModels\SensitiveWordModel;

class SensitiveWordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'SensitiveWordData';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SensitiveWordModel());
        // data
        $array = DB::SELECT("SELECT `COLUMN_NAME`, `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . env("DB_DATABASE") . "' AND `TABLE_NAME` = 'sensitive_word_data'");
        foreach ($array as $row) {
            $grid->column($row->COLUMN_NAME, $row->COLUMN_COMMENT);
        }

        // filter
        $grid->filter(function($filter){
            // remove default id filter
            $filter->disableIdFilter();

            // filter
            $array = DB::select("SELECT `COLUMN_NAME`, `COLUMN_COMMENT` FROM information_schema.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . env("DB_DATABASE") . "' AND `TABLE_NAME` = 'sensitive_word_data' AND `COLUMN_KEY` IN ('PRI', 'MUL')");
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

        $grid->setActionClass(ContextMenuActions::class);

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SensitiveWordModel());



        return $form;
    }
}
