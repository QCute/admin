<?php

namespace App\Admin\Controllers\GameConfigureControllers;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\Controllers\SwitchServerController;

class ConfigureListController extends Controller
{
    public function action()
    {
        // act action
        $action = request()->input("action", "");
        switch ($action)
        {
            case "load":
            {
                // make
                exec(env("SERVER_CODE_PATH") . "/script/shell/maker.sh data " . basename(request()->input("file"), ".erl") . " 2>&1", $result);
                if (implode("", $result) != "ok") {return implode("", $result);}
                // compile
                unset($result);
                exec(env("SERVER_CODE_PATH") . "/script/shell/maker.sh debug " . basename(request()->input("file"), ".erl") . " 2>&1", $result);
                if (implode("", $result) != "ok") {return implode("", $result);}
                // reload
                unset($result);
                exec(env("SERVER_CODE_PATH") . "/script/shell/runner.sh " . SwitchServerController::getCurrentServer() . " load " . basename(request()->input("file"), ".erl") . " 2>&1", $result);
                // handle result
                $result = implode("", $result);
                if($result == "ok") 
                    return "toastr.success(" . json_encode(trans("admin.succeeded")) . ")";
                else
                    return "toastr.error(" . json_encode($result) . ")";
            }break;
            case "erl":
            {
                exec(env("SERVER_CODE_PATH") . "/script/shell/maker.sh data " . basename(request()->input("file"), ".erl") . " 2>&1", $result);
                // handle result
                $result = implode("", $result);
                if($result == "ok") 
                    return "toastr.success(" . json_encode(trans("admin.succeeded")) . ")";
                else
                    return "toastr.error(" . json_encode($result) . ")";
            }break;
            case "lua":
            {
                exec(env("SERVER_CODE_PATH") . "/script/shell/maker.sh lua " . basename(request()->input("file"), ".lua") . " 2>&1", $result);
                // handle result
                $result = implode("", $result);
                if($result == "ok") 
                    return "toastr.success(" . json_encode(trans("admin.succeeded")) . ")";
                else
                    return "toastr.error(" . json_encode($result) . ")";
            }break;
            case "js":
            {
                exec(env("SERVER_CODE_PATH") . "/script/shell/maker.sh js " . basename(request()->input("file"), ".js") . " 2>&1", $result);
                // handle result
                $result = implode("", $result);
                if($result == "ok") 
                    return "toastr.success(" . json_encode(trans("admin.succeeded")) . ")";
                else
                    return "toastr.error(" . json_encode($result) . ")";
            }break;
            default:
            {
                if (empty($action)) 
                    return "";
                else 
                    return "toastr.error(" . json_encode("unknown action: " . request()->input("action")) . ")";
            }
        }
    }

    public function showErl(Content $content)
    {
        // action
        $result = $this->action();
        // read configure from data script
        $script = file_get_contents(env("SERVER_CODE_PATH") . "/script/make/script/data_script.erl");
        $script = substr($script, strpos($script, "data() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.erl/", $script, $file);
        $html = implode("", array_map(function($row, $name){ return "<tr><td>{$name}</td><td>{$row}</td><td><a class='action' href='erl-configure?action=erl&file={$row}'>" . trans("admin.generate") . "</a> | <a class='action' href='erl-configure?action=load&file={$row}'>" . trans("admin.update") . "</a></td></tr>"; }, $file[0], $name[0]));
        return $content->body("
            <style>.action{cursor: pointer;}</style>
            <style>.panel{border-radius: 0px;}</style>
            <script>$(document).ready(function(){{$result}})</script>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.description") . "</th><th>" . trans("admin.file") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }

    public function showLua(Content $content)
    {
        // action
        $result = $this->action();
        // read configure from data script
        $script = file_get_contents(env("SERVER_CODE_PATH") . "/script/make/script/lua_script.erl");
        $script = substr($script, strpos($script, "lua() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.lua/", $script, $file);
        $html = implode("", array_map(function($row, $name){ return "<tr><td>{$name}</td><td>{$row}</td><td><a class='action' href='erl-configure?action=erl&file={$row}'>" . trans("admin.generate") . "</a></td></tr>"; }, $file[0], $name[0]));
        return $content->body("
            <style>.action{cursor: pointer;}</style>
            <style>.panel{border-radius: 0px;}</style>
            <script>$(document).ready(function(){{$result}})</script>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.description") . "</th><th>" . trans("admin.file") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }

    public function showJs(Content $content)
    {
        // action
        $result = $this->action();
        // read configure from data script
        $script = file_get_contents(env("SERVER_CODE_PATH") . "/script/make/script/js_script.erl");
        $script = substr($script, strpos($script, "js() ->"), strlen($script));
        // extract meta info
        preg_match_all("/(?<=\%\%).*/", $script, $name);
        preg_match_all("/\w+\.js/", $script, $file);
        $html = implode("", array_map(function($row, $name){ return "<tr><td>{$name}</td><td>{$row}</td><td><a class='action' href='erl-configure?action=erl&file={$row}'>" . trans("admin.generate") . "</a></td></tr>"; }, $file[0], $name[0]));
        return $content->body("
            <style>.action{cursor: pointer;}</style>
            <style>.panel{border-radius: 0px;}</style>
            <script>$(document).ready(function(){{$result}})</script>
            <div class='panel panel-default'>
                <table class='table'>
                    <thead><tr><th>" . trans("admin.description") . "</th><th>" . trans("admin.file") . "</th><th>" . trans("admin.operation") . "</th></tr></thead>
                    {$html}
                </table>
            </div>
        ");
    }
}
