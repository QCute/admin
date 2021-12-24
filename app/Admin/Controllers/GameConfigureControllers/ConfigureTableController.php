<?php

namespace App\Admin\Controllers\GameConfigureControllers;

use App\Admin\Controllers\SwitchServerController;
use App\Admin\Models\GameConfigureModels\ConfigureTableModel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConfigureTableController extends AdminController
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
     */
    protected function grid(): Grid
    {
        $url = request()->url();
        $grid = new Grid(new ConfigureTableModel());
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
                "OPERATION" => false,
                "NAME" => "user_name",
                "COMMENT" => trans("admin.username"),
            ],
            (object)[
                "OPERATION" => false,
                "NAME" => "time",
                "COMMENT" => trans("admin.time"),
            ],
            (object)[
                "OPERATION" => false,
                "NAME" => "state",
                "COMMENT" => trans("admin.state"),
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
                ->display(function () use ($url, $row) {
                    if ($row->OPERATION) {
                        $href = "$url?action=export&table=$this->TABLE_NAME&xml=$this->TABLE_COMMENT";
                        return "<a href=\"$href\">" . trans("admin.export"). "</a>";
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
                if ($row->NAME == "username") continue;
                if ($row->NAME == "time") continue;
                if ($row->NAME == "state") continue;
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
            $export->filename("configure_table");
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
        $form = new Form(new ConfigureTableModel());
        $form->ignore(['updated_at', 'created_at']);
        return $form;
    }

    /**
     * Action interface.
     *
     * @param Content $content
     * @param  string $action
     * @return Content
     * @throws Exception
     */
    public function action(Content $content, string $action): Content
    {
        // action
        if ($action == "export") {
            // ensure dir
            $path = storage_path("app/admin/xml/");
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            // generate xml file
            $table = request()->input("table");
            $basename = request()->input("xml");
            $filename = $basename . ".xml";
            SwitchServerController::executeMakerScript(["xml", $table, "xml/"]);
            SwitchServerController::pullFile("xml/$filename", $path . $filename);
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
            // import table data
            SwitchServerController::pushFile($pathname, "xml/$filename");
            $result = SwitchServerController::executeMakerScript(["table", "xml/$filename"]);
            // import log
            $schema = SwitchServerController::getCurrentServer();
            $data = SwitchServerController::getDB()
                ->table("information_schema.TABLES")
                ->select("TABLE_NAME")
                ->where("TABLE_SCHEMA", "=", DB::raw("DATABASE()"))
                ->where("TABLE_COMMENT", "=", $basename)
                ->get()
                ->toArray();
            $data = ["user_name" => Auth::user()->name, "table_schema" => $schema, "table_name" => $data[0]->TABLE_NAME, "table_comment" => $basename];
            DB::table("table_import_log")->insert($data);
            return $this->displayIndex($content)->withSuccess(trans("admin.import") . trans("admin.succeeded"), $result);
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
