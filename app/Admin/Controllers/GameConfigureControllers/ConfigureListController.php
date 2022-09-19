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
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
                "NAME" => "status",
                "COMMENT" => trans("admin.status") . " (<a href='$url?action=submit'><i class='fa fa-cloud-upload'></i><span class='hidden-xs'> " . trans("admin.submit"). "</span></a>)",
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
                        $export = "<a href='$url?action=export&file=$this->file&xml=$this->description'><i class='fa fa-upload'></i><span class='hidden-xs'> " . trans("admin.export"). "</span></a>";
                        $generate = "<a href='$url?action=$path&file=$this->file'><i class='fa fa-refresh'></i><span class='hidden-xs'> " . trans("admin.generate"). "</span></a>";
                        $download = "<a href='$url?action=download&file=$this->file'><i class='fa fa-download'></i><span class='hidden-xs'> " . trans("admin.download"). "</span></a>";
                        return "$export | $generate | $download";
                    } else if ($row->NAME == "OPERATION") {
                        $export = "<a href='javascript:void(0)' style='color: black; pointer-events: none'><strong>" . trans("admin.export"). "</strong></a>";
                        $generate = "<a href='javascript:void(0)' style='color: black; pointer-events: none'><strong>" . trans("admin.generate"). "</strong></a>";
                        $download = "<a href='javascript:void(0)' style='color: black; pointer-events: none'><strong>" . trans("admin.download"). "</strong></a>";
                        return "$export | $generate | $download";
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
                if ($row->NAME == "status") continue;
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
        if ($action == "export") {
            // ensure dir
            $path = storage_path("app/admin/xml/");
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            // generate xml file
            $file = request()->input("file");
            $basename = request()->input("xml");
            $filename = "$basename.xml";
            SwitchServerController::executeMakerScript(["sheet", basename($file), "xml/"]);
            SwitchServerController::pullFile("xml/$filename", "$path/$filename");
            // export log
            $schema = SwitchServerController::getCurrentServer();
            $data = ["user_name" => Auth::user()->name, "table_schema" => $schema, "table_name" => $basename, "table_comment" => $basename, "state" => "1"];
            DB::table("table_import_log")->insert($data);
            // download xml file
            // pjax use location.href redirection to download file or use ajax
            $url = request()->url();
            $content->body("
                <a href='$url-export?file=$filename' target='_blank' style='display: none;' id='export'></a>
                <script>document.getElementById('export').click();</script>
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
            SwitchServerController::pushFile("$path/$filename", "xml/$filename");
            $result = SwitchServerController::executeMakerScript(["collection", "xml/$filename"]);
            // import log
            $data = ["user_name" => Auth::user()->name, "table_schema" => $server, "table_name" => $basename, "table_comment" => $basename, "state" => "0"];
            DB::table("table_import_log")->insert($data);
            return $this->displayIndex($content)->withSuccess(trans("admin.import") . trans("admin.succeeded"), $result);
        } else if ($action == "submit") {
            // repository commit/push
            $path = request()->path();
            $current = SwitchServerController::getCurrentServer();
            $server = SwitchServerController::getServer($current);
            if (is_int(strpos($path, "erl"))) {
                // the server dir
                return $this->repository_commit($content, "$server->server_root/src/module/");
            } else if (is_int(strpos($path, "lua"))) {
                // the configure dir
                return $this->repository_commit($content, $server->configure_root);
            } else if (is_int(strpos($path, "js"))) {
                // the configure dir
                return $this->repository_commit($content, $server->configure_root);
            } else {
                throw new Exception("Unknown Path: $path");
            }
        } else if ($action == "download") {
            // download erl/lua/js file
            $file = request()->input("file");
            $filename = basename($file);
            $type = substr(strrchr($file, "."), 1);
            // ensure dir
            $path = storage_path("app/admin/$type/");
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            SwitchServerController::pullFile($file, "$path/$filename");
            // pjax use location.href redirection to download file or use ajax
            $url = request()->url();
            $content->body("
                <a href='$url-download?type=$type&file=$filename' target='_blank' style='display: none;' id='download'></a>
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

    public function export(): BinaryFileResponse
    {
        $file = request()->input("file", "");
        $path = storage_path("app/admin/xml/$file");
        return response()->download($path, $file, ["Content-Type: text/xml"]);
    }

    public function download(): BinaryFileResponse
    {
        $type = request()->input("type", "");
        $file = request()->input("file", "");
        $path = storage_path("app/admin/$type/$file");
        return response()->download($path, $file, ["Content-Type: application/json"]);
    }

    private function repository_commit(Content $content, string $path = null): Content
    {
        // try parse as git repository
        try {
            // try read remote url
            $url = SwitchServerController::execute(["git", "config", "--get", "remote.origin.url"], $path);
            // connect by http
            if (str_starts_with($url, "http")) {
                $error = "Git repository not connected by SSH: $url";
                return $this->displayIndex($content)->withError(admin_trans("admin.failed"), $error);
            }
            try {
                // git use ssh, auth by public key, but without key passphrase
                // SwitchServerController::execute(["git", "stash", "--include-untracked"], $path);
                // SwitchServerController::execute(["git", "stash", "pop", "--quiet"], $path);
                // SwitchServerController::execute(["git", "checkout", "--ours", "."], $path);
                SwitchServerController::execute(["git", "add", "."], $path);
                SwitchServerController::execute(["git", "commit", "--message=Add Configure Data"], $path);
                $branch = SwitchServerController::execute(["git", "branch", "--show-current"]);
                SwitchServerController::execute(["git", "pull", "origin", trim($branch), "--rebase"], $path);
                SwitchServerController::execute(["git", "push", "origin", trim($branch), "--force-with-lease"], $path);
            } catch (ProcessFailedException $exception) {
                $error = str_replace("\n", "<br>", $exception->getProcess()->getOutput());
                return $this->displayIndex($content)->withError($exception->getProcess()->getCommandLine(), $error);
            } catch (Exception $exception) {
                $error = str_replace("\n", "<br>", $exception->getMessage());
                return $this->displayIndex($content)->withError(admin_trans("admin.failed"), $error);
            }
        } catch (Exception) {
            // suppress error when dir not a git repository
        }
        // try parse as svn repository
        try {
            // try read remote url
            $url = SwitchServerController::execute(["svn", "info", "--show-item", "repos-root-url"], $path);
            // connect by svn or http
            if (str_starts_with($url, "svn://") || str_starts_with($url, "http")) {
                $error = "SVN repository not connected by SSH: $url";
                return $this->displayIndex($content)->withError(admin_trans("admin.failed"), $error);
            }
            try {
                // svn use ssh, auth by public key, but without key passphrase
                // SwitchServerController::execute(["svn", "update", "--non-interactive"], $path);
                // SwitchServerController::execute(["svn", "resolve", "--accept", "mine-full", "*", "--force"], $path);
                SwitchServerController::execute(["svn", "add", ".", "--no-ignore", "--force"], $path);
                SwitchServerController::execute(["svn", "commit", "--message", "Add Configure Data"], $path);
            } catch (ProcessFailedException $exception) {
                $error = str_replace("\n", "<br>", $exception->getProcess()->getOutput());
                return $this->displayIndex($content)->withError($exception->getProcess()->getCommandLine(), $error);
            } catch (Exception $exception) {
                $error = str_replace("\n", "<br>", $exception->getMessage());
                return $this->displayIndex($content)->withError(admin_trans("admin.failed"), $error);
            }
        } catch (Exception) {
            // suppress error when dir not a svn repository
        }
        return $this->displayIndex($content)->withSuccess(trans("admin.succeeded"));
    }
}
