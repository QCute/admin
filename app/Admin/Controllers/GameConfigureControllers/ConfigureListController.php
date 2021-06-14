<?php

namespace App\Admin\Controllers\GameConfigureControllers;

use Exception;
use Symfony\Component\Process\Process;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use App\Admin\Models\GameConfigureModels\ConfigureListModel;

class ConfigureListController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     * @throws Exception
     */
    public function index(Content $content): Content
    {
        $action = request()->input("action", "");
        if (!empty($action)) {
            return $this->action($content, $action);
        }
        return $this->displayIndex($content);
    }

    /**
     * Index.
     *
     * @param Content $content
     *
     * @return Content
     * @throws Exception
     */
    public function displayIndex(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     * @throws Exception
     */
    protected function grid(): Grid
    {
        $path = request()->path();
        $route = $this->getRoute($path);
        $grid = new Grid(new ConfigureListModel($route));
        $data = [
            (object)[
                "OPERATION" => false,
                "NAME" => "description",
                "COMMENT" => trans("admin.description"),
            ],
            (object)[
                "OPERATION" => false,
                "NAME" => "file",
                "COMMENT" => trans("admin.file"),
            ],
            (object)[
                "OPERATION" => true,
                "NAME" => "TABLE_SCHEMA",
                "COMMENT" => trans("admin.operation"),
            ],
        ];
        foreach ($data as $row) {
            $grid
                ->column($row->NAME, $row->COMMENT)
                ->style("min-width:8em")
                ->display(function () use ($path, $route, $row) {
                    if ($row->OPERATION) {
                        $href = "{$path}?action={$route}&file={$this->file}";
                        return "<a href='{$href}'>" . trans("admin.generate"). "</a>";
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
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(): Form
    {
        return new Form(new ConfigureListModel());
    }

    /**
     * Make a form builder.
     *
     * @param string $path
     *
     * @return string
     * @throws Exception
     */
    private function getRoute(string $path): string
    {
        if (is_int(strpos($path, "erl"))) {
            return "erl";
        } else if (is_int(strpos($path, "lua"))) {
            return "lua";
        } else if (is_int(strpos($path, "js"))) {
            return "js";
        } else {
            throw new Exception("Unknown Path: $path");
        }
    }

    /**
     * @param Content $content
     * @param string $action
     *
     * @return Content
     * @throws Exception
     */
    public function action(Content $content, string $action): Content
    {
        // act action
        switch ($action)
        {
            case "erl":
            {
                $file = basename(request()->input("file"), ".erl");
                // generate
                $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "data", $file, ["PATH" => `echo \$PATH`]]);
                $process->run();
                if (!$process->isSuccessful()) {
                    return $this->displayIndex($content)->withError($process->getErrorOutput());
                }
                $result = $process->getOutput();
                $content->withSuccess($result);
                // compile
                $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "release", $file, ["PATH" => `echo \$PATH`]]);
                $process->run();
                if (!$process->isSuccessful()) {
                    return $this->displayIndex($content)->withError($process->getErrorOutput());
                }
                $result = $process->getOutput();
                $content->withSuccess($result);
                // load
                $process = new Process([env("SERVER_PATH") . "/script/shell/run.sh", "-load", $file, ["PATH" => `echo \$PATH`]]);
                $process->run();
                if (!$process->isSuccessful()) {
                    return $this->displayIndex($content)->withError($process->getErrorOutput());
                }
                $result = $process->getOutput();
                $content->withSuccess($result);
                // index page
                return $this->displayIndex($content);
            }
            case "lua":
            {
                $file = basename(request()->input("file"), ".lua");
                // generate
                $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "lua", $file, ["PATH" => `echo \$PATH`]]);
                $process->run();
                // result
                if (!$process->isSuccessful()) {
                    return $this->displayIndex($content)->withError($process->getErrorOutput());
                }
                return $this->displayIndex($content)->withSuccess(trans("admin.generate") . trans("admin.succeeded"));
            }
            case "js":
            {
                $file = basename(request()->input("file"), ".lua");
                // generate
                $process = new Process([env("SERVER_PATH") . "/script/shell/maker.sh", "js", $file, ["PATH" => `echo \$PATH`]]);
                $process->run();
                // result
                if (!$process->isSuccessful()) {
                    return $this->displayIndex($content)->withError($process->getErrorOutput());
                }
                return $this->displayIndex($content)->withSuccess(trans("admin.generate") . trans("admin.succeeded"));
            }
            default:
            {
                return $this->displayIndex($content)->withError("Unknown Action: {$action}");
            }
        }
    }

}
