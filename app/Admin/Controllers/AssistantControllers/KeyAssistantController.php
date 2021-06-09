<?php

namespace App\Admin\Controllers\AssistantControllers;

use Exception;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use App\Admin\Models\AssistantModels\KeyAssistantModel;

class KeyAssistantController extends AdminController
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
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function displayIndex(Content $content): Content
    {
        return $content
            ->title($this->title())
            ->description($this->description['create'] ?? trans('admin.create'))
            ->body($this->form());
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form(): Form
    {
        $form = new Form(new KeyAssistantModel());
        $form->setTitle(trans("admin.generate"));
        $options = [
            "ED255119" => "ed25519",
            "ECDSA" => "ecdsa",
            "RSA" => "rsa",
        ];
        $form->select("type", trans("admin.type"))->options($options);
        $form->password("passphrase", trans("admin.password"));
        $form->password("passphraseAgain", trans("admin.ensure") . trans("admin.password"));
        $form->text("name", trans("admin.name"));
        $form->hidden("action")->value("generate");

        $form->disableViewCheck();
        $form->disableCreatingCheck();
        $form->disableEditingCheck();

        $form->tools(function (Form\Tools $tools) {
            // remove list
            $tools->disableList();
            // remove delete
            $tools->disableDelete();
            // remove view
            $tools->disableView();
        });

        $form->setAction(request()->path());

        return $form;
    }

    public function action(Content $content, string $action)
    {
        $type = request()->input('type', '');
        if(empty($type)) {
            return $this->displayIndex($content)->withError("请选择类型");
        }
        $passphrase = request()->input('passphrase', '');
        if(empty($passphrase)) {
            return $this->displayIndex($content)->withError("请输入密码");
        }
        $name = request()->input('name', '');
        if(empty($name)) {
            return $this->displayIndex($content)->withError("请输入名称");
        }
        // check passphrase
        if(8 >= strlen($passphrase)) {
            return $this->displayIndex($content)->withError("密码长度最短8位");
        }
        if(preg_match("/\d+/", $passphrase) == false) {
            return $this->displayIndex($content)->withError("密码需包含数字");
        }
        if(preg_match("/[a-z]+/", $passphrase) == false) {
            return $this->displayIndex($content)->withError("密码需包含小写字母");
        }
        if(preg_match("/[A-Z]+/", $passphrase) == false) {
            return $this->displayIndex($content)->withError("密码需包含大写字母");
        }
        if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $passphrase) == false) {
            return $this->displayIndex($content)->withError("密码需包含特殊字符");
        }

        // file
        $file = storage_path() . "/app/id_{$type}";
        $pub_file = storage_path() . "/app/id_{$type}.pub";
        try {
            unlink($file);
            unlink($pub_file);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        // generate
        $process = new Process(['ssh-keygen', '-q', '-t', $type, '-b', '4096', '-f', $file, '-N', $passphrase, '-C', $name]);
        $process->run();
        // result
        if (!$process->isSuccessful()) {
            return $this->displayIndex($content)->withError($process->getErrorOutput());
        }
        $result = $process->getOutput();
        if (!empty($result)) {
            return $this->displayIndex($content)->withError($result);
        }
        // result
        $result = json_encode([
            "result" => "",
            "key_name" => basename($file),
            "key" => base64_encode(file_get_contents($file)),
            "pub_name" => basename($pub_file),
            "pub" => base64_encode(file_get_contents($pub_file))
        ]);
        // remove file
        try {
            unlink($file);
            unlink($pub_file);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return $result;
    }


    public function index2(Content $content)
    {
        return $content->title('')->body("
            <script type='text/javascript'>
                function generate() {
                    $.ajax({
                        url: 'key-assistant-generate',
                        type: 'POST',
                        dataType: 'json',
                        data: $('#form').serialize(),
                        success: function(json) {
                            if (json.result === '') {
                                download(json['key_name'], window.atob(json['key']));
                                download(json['pub_name'], window.atob(json['pub']));
                            } else {
                                swal(json.result, '', 'error');
                            }
                        },
                        error : (result) => alert(result)
                    });
                }
                function download(filename, text) {
                    var pom = document.createElement('a');
                    pom.setAttribute('href', URL.createObjectURL(new Blob([text], {type: 'application/octet-stream'})));
                    pom.setAttribute('download', filename);
                    if (document.createEvent) {
                        var event = document.createEvent('MouseEvents');
                        event.initEvent('click', true, true);
                        pom.dispatchEvent(event);
                    }
                    else {
                        pom.click();
                    }
                }
            </script>
            <div class='box box-info'>
                <div class='box-header with-border'>" . trans("admin.generate") . "</div>
                <form id='form' name='form' class='form-horizontal' action=''>
                    " . csrf_field() . "
                    <div class='box-body'>
                        <div class='form-group'>
                            <label for='type' class='col-sm-4 asterisk control-label'>类型: </label>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><i class='fa fa-cogs'></i></span>
                                    <select class='form-control' name='type' style='outline:none;'>
                                        <option value='ed25519'>ed25519(推荐)</option>
                                        <option value='rsa'>rsa</option>
                                        <option value='ecdsa'>ecdsa</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class='form-group'>
                            <label for='passphrase' class='col-sm-4 asterisk control-label'>密码: </label>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><i class='fa fa-key'></i></span>
                                    <input type='text' class='form-control' name='passphrase' placeholder='' aria-describedby='basic-addon3'>
                                </div>
                            </div>
                        </div>
                        
                        <div class='form-group'>
                            <label for='comment' class='col-sm-4 asterisk control-label'>名字: </label>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><i class='fa fa-tags'></i></span>
                                    <input type='text' class='form-control' name='comment' placeholder='' aria-describedby='basic-addon3'>
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label for='name' class='col-sm-4 control-label'></label>
                            <div class='col-sm-3'>
                                <div class='input-group'>
                                    <span class='input-group-addon'><i class='fa fa-check'></i></span>
                                    <input type='button' class='form-control' value='确定' onclick='generate()' />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div></div>
            <br/>
        ");
    }
}
