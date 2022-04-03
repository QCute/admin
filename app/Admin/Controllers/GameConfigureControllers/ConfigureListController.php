<?php

namespace App\Admin\Controllers\GameConfigureControllers;

use App\Admin\Controllers\SwitchServerController;
use App\Admin\Models\GameConfigureModels\ConfigureListModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * @return Content
     * @throws Exception
     */
    public function index(Content $content): Content
    {
        // check
        $server = SwitchServerController::getCurrentServer();
        if (empty($server)) {
            return $content
                ->title($this->title())
                ->withWarning("Could not found current server");
        }
        // view
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
        $url = request()->url();
        $path = request()->path();
        $grid = new Grid(new ConfigureListModel());
        $grid->paginate(env("ADMIN_PER_PAGE", 20));
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
                "NAME" => "OPERATION",
                "COMMENT" => trans("admin.operation"),
            ],
        ];
        foreach ($data as $row) {
            $grid
                ->column($row->NAME, $row->COMMENT)
                ->style("min-width:8em")
                ->display(function () use ($url, $path, $row) {
                    if ($row->OPERATION) {
                        $generate = "<a href='$url?action=$path&file=$this->file'>" . trans("admin.generate"). "</a>";
                        $export = "<a href='$url?action=$path-export&file=$this->file&xml=$this->description'>" . trans("admin.export"). "</a>";
                        return "$generate | $export";
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
        $grid->export(function ($export) {
            $export->filename("configure_list");
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
        return new Form(new ConfigureListModel());
    }

    /**
     * Action interface.
     *
     * @param Content $content
     * @param string $action
     * @return Content
     * @throws Exception
     */
    public function action(Content $content, string $action): Content
    {
        // act action
        if (is_int(strpos($action, "export"))) {
            // ensure dir
            $path = storage_path("app/admin/xml/");
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            // generate xml file
            $file = request()->input("file");
            $basename = request()->input("xml");
            $filename = $basename . ".xml";
            SwitchServerController::executeMakerScript(["sheet", $file, "xml/"]);
            SwitchServerController::pullFile("xml/$filename", $path . $filename);
            // download xml file
            // pjax use location.href redirection to download file or use ajax
            $url = request()->url();
            $content->body("
                <a href='$url-download?file=$filename' target='_blank' style='display: none;' id='download'></a>
                <script>document.getElementById('download').click();</script>
            ");
            return $this->displayIndex($content);
        } else if (is_int(strpos($action, "erl"))) {
            $file = basename(request()->input("file"), ".erl");
            // generate
            SwitchServerController::executeMakerScript(["data", $file]);
            // compile
            SwitchServerController::executeMakerScript(["release", $file]);
            // load
            $name = SwitchServerController::getCurrentServer();
            $result = SwitchServerController::executeRunScript([$name, "load", $file]);
            // index page
            return $this->displayIndex($content)->withSuccess($result);
        } else if (is_int(strpos($action, "lua"))) {
            $file = basename(request()->input("file"), ".lua");
            // generate
            SwitchServerController::executeMakerScript(["lua", $file]);
            return $this->displayIndex($content)->withSuccess(trans("admin.generate") . trans("admin.succeeded"));
        } else if (is_int(strpos($action, "js"))) {
            $file = basename(request()->input("file"), ".js");
            // generate
            SwitchServerController::executeMakerScript(["js", $file]);
            return $this->displayIndex($content)->withSuccess(trans("admin.generate") . trans("admin.succeeded"));
        } else {
            return $this->displayIndex($content)->withError("Unknown Action: $action");
        }
    }

    public function download(): BinaryFileResponse
    {
        $file = request()->input("file", "");
        $path = storage_path("app/admin/xml/$file");
        return response()->download($path, $file, ["Content-Type: text/xml"]);
    }
}
