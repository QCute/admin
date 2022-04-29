<?php

namespace App\Admin\Controllers\GameConfigureControllers;

use App\Admin\Controllers\SwitchServerController;
use App\Admin\Models\GameConfigureModels\ConfigureListModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $grid->header(function () use ($url) {
            return "
<style>.action{cursor: pointer;}</style>
<div class=input-group file-caption-main'>
    <div class='file-caption form-control kv-fileinput-caption icon-visible'>
        <span class='file-caption-icon'><i class='glyphicon glyphicon-file'></i></span>
        <input class='file-caption-name' onkeydown='return false;' onpaste='return false;' id='filename' placeholder='" . trans("admin.choose_file") . "'>
    </div>
    <div class='input-group-btn input-group-append'>
        <form action='$url' method='POST' enctype='multipart/form-data' pjax-container>
            " . csrf_field() . "
            <input type='hidden' name='action' value='import'>
            <div class='btn btn-primary btn-file'>
                <i class='glyphicon glyphicon-folder-open'></i>
                <span>" . trans("admin.browse") . "</span>
                <input type='file' onchange='document.getElementById(\"filename\").value = this.value.split(/\\\|\//g).pop()' name='xml'>
            </div>
            <input class='btn btn-primary' type='submit' value='" . trans("admin.import") . "'/>
        </form>
    </div>
</div>
            ";
        });
        $data = [
            (object)[
                "NAME" => "description",
                "COMMENT" => trans("admin.description"),
            ],
            (object)[
                "NAME" => "file",
                "COMMENT" => trans("admin.file"),
            ],
            (object)[
                "NAME" => "user_name",
                "COMMENT" => trans("admin.username"),
            ],
            (object)[
                "NAME" => "time",
                "COMMENT" => trans("admin.time"),
            ],
            (object)[
                "NAME" => "state",
                "COMMENT" => trans("admin.state"),
            ],
            (object)[
                "NAME" => "OPERATION",
                "COMMENT" => trans("admin.operation"),
            ],
        ];
        foreach ($data as $row) {
            $grid
                ->column($row->NAME, $row->COMMENT)
                ->style("min-width:8em")
                ->display(function () use ($url, $path, $row) {
                    if ($row->NAME == "OPERATION" && (empty($this->action) || $this->action == "0")) {
                        $generate = "<a href='$url?action=$path&file=$this->file'><strong>" . trans("admin.generate"). "</strong></a>";
                        $export = "<a href='$url?action=$path-export&file=$this->file&xml=$this->description'><strong>" . trans("admin.export"). "</strong></a>";
                        return "$generate | $export";
                    } else if ($row->NAME == "OPERATION") {
                        $generate = "<a href='javascript:void(0)' style='color: black; pointer-events: none'><strong>" . trans("admin.generate"). "</strong></a>";
                        $export = "<a href='javascript:void(0)' style='color: black; pointer-events: none'><strong>" . trans("admin.export"). "</strong></a>";
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
                if ($row->NAME == "user_name") continue;
                if ($row->NAME == "time") continue;
                if ($row->NAME == "state") continue;
                if ($row->NAME == "OPERATION") continue;
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
            // export log
            $schema = SwitchServerController::getCurrentServer();
            $data = ["user_name" => Auth::user()->name, "table_schema" => $schema, "table_name" => $basename, "table_comment" => $basename, "state" => "1"];
            DB::table("table_import_log")->insert($data);
            // download xml file
            // pjax use location.href redirection to download file or use ajax
            $url = request()->url();
            $content->body("
                <a href='$url-download?file=$filename' target='_blank' style='display: none;' id='download'></a>
                <script>document.getElementById('download').click();</script>
            ");
            return $this->displayIndex($content);
        } else if ($action == "import") {
            // file upload
            $path = storage_path("app/admin/xml/");
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $file = request()->file("xml");
            $filename = $file->getClientOriginalName();
            $basename = basename($filename, ".xml");
            $pathname = $path . $filename;
            $result = $file->storeAs("xml", $filename, ["disk" => "admin"]);
            // handle store result
            if(!$result) {
                return $this->displayIndex($content)->withError(trans("admin.upload") . trans("admin.error"));
            }
            // check state
            $server = SwitchServerController::getCurrentServer();
            $sub = DB::table("table_import_log")
                ->select(Db::raw("MAX(id)"))
                ->where("table_schema", $server)
                ->where("table_comment", $basename)
                ->groupBy("table_name");
            $log = DB::table("table_import_log")
                ->select(["user_name", "table_name", "table_comment", "time", "state"])
                ->whereRaw(DB::raw("id in ({$sub->toSql()})"), $sub->getBindings())
                ->get()
                ->toArray();
            if(!empty($log) && $log[0]->state !== 0 && $log[0]->user_name != Auth::user()->name) {
                return $this->displayIndex($content)->withError("Configure Lock By: {$log[0]->user_name}");
            }
            // import table data
            SwitchServerController::pushFile($pathname, "xml/$filename");
            $result = SwitchServerController::executeMakerScript(["collection", "xml/$filename"]);
            // import log
            $data = ["user_name" => Auth::user()->name, "table_schema" => $server, "table_name" => $basename, "table_comment" => $basename, "state" => "0"];
            DB::table("table_import_log")->insert($data);
            return $this->displayIndex($content)->withSuccess(trans("admin.import") . trans("admin.succeeded"), $result);
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
