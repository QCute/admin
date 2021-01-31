<?php

namespace App\Admin\Controllers\AssistantControllers;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;

class KeyAssistantController extends Controller
{
    public function generate(Content $content)
    {
        $type = request()->input('type', '');
        if(empty($type)) return json_encode(["result" => "请选择类型"]);
        $passphrase = request()->input('passphrase', '');
        if(empty($passphrase)) return json_encode(["result" => "请输入密码"]);
        $comment = request()->input('comment', '');
        if(empty($comment)) return json_encode(["result" => "请输入注释"]);
        // check passphrase
        if(8 >= strlen($passphrase)) return json_encode(["result" => "密码长度最短8位"]);
        if(preg_match("/\d+/", $passphrase) == false) return json_encode(["result" => "密码需包含数字"]);
        if(preg_match("/[a-z]+/", $passphrase) == false) return json_encode(["result" => "密码需包含小写字母"]);
        if(preg_match("/[A-Z]+/", $passphrase) == false) return json_encode(["result" => "密码需包含大写字母"]);
        if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $passphrase) == false) return json_encode(["result" => "密码需包含特殊字符"]);
        // file
        $file = storage_path() . "/app/id_{$type}";
        $pub_file = storage_path() . "/app/id_{$type}.pub";
        try {
            unlink($file);
            unlink($pub_file);
        } catch (\Exception $exception) {}
        // generate
        $result = shell_exec("ssh-keygen -q -t {$type} -b 4096 -f {$file} -N {$passphrase} -C {$comment} 2>&1");
        if (!empty($result)) return json_encode(["result" => $result]);
        // result
        $result = json_encode(["result" => "", "key_name" => basename($file), "key" => base64_encode(file_get_contents($file)), "pub_name" => basename($pub_file), "pub" => base64_encode(file_get_contents($pub_file))]);
        // remove file
        try {
            unlink($file);
            unlink($pub_file);
        } catch (\Exception $exception) {}
        return $result;
    }

    public function index(Content $content)
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
