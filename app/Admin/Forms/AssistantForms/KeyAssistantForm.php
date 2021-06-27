<?php

namespace App\Admin\Forms\AssistantForms;

use Exception;
use Symfony\Component\Process\Process;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Encore\Admin\Widgets\Form;
use Encore\Admin\Traits\DefaultDatetimeFormat;

class KeyAssistantForm extends Form
{
    use DefaultDatetimeFormat;

    /**
     * The form title.
     *
     * @var  string
     */
    public $title = '';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $type = $request->input('type', '');
        $passphrase = $request->input('passphrase', '');
        $passphrase_again = $request->input('passphrase_again', '');
        if ($passphrase != $passphrase_again) {
            admin_error(trans("admin.password") . trans("admin.not_match"));
            return back();
        }
        $name = $request->input('name', '');
        // file
        $file = storage_path("app/admin/keys/id_{$type}");
        $pub_file = storage_path("app/admin/keys/id_{$type}.pub");
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
        if (!$process->isSuccessful() || !empty($process->getErrorOutput())) {
            admin_error($process->getErrorOutput());
            return back();
        }
        $result = $process->getOutput();
        if (!empty($result)) {
            admin_error($process->getErrorOutput());
            return back();
        }
        // pass to cookie
        setcookie("type", base64_encode($type));
        setcookie("key", base64_encode(file_get_contents($file)));
        setcookie("pub_key", base64_encode(file_get_contents($pub_file)));
        // remove file
        try {
            unlink($file);
            unlink($pub_file);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $options = [
            "ed25519" => "ed25519",
            "ed25519-sk" => "ed25519-sk",
            "ecdsa" => "ecdsa",
            "ecdsa-sk" => "ecdsa-sk",
            "dsa" => "dsa",
            "rsa" => "rsa",
        ];
        $this->title = trans("admin.generate");
        $this
            ->select("type", trans("admin.type"))
            ->options($options)
            ->required();
        //  /^[a-z|A-Z|0-9\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\（|\）|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|]+$/;
        $this
            ->password("passphrase", trans("admin.password"))
            ->rules(["required", "min:8", "regex:/^[a-z|A-Z|0-9\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\（|\）|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|]+$/"], [
                "min" => "密码长度最短8位",
                "regex" => "密码需要包含数字，大小写字母以及特殊字符"
            ]);
        $this
            ->password("passphrase_again", trans("admin.confirm") . trans("admin.password"))
            ->rules(["required", "min:8", "regex:/^[a-z|A-Z|0-9\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\（|\）|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|]+$/"], [
                "min" => "密码长度最短8位",
                "regex" => "密码需要包含数字，大小写字母以及特殊字符"
            ]);
        $this
            ->text("name", trans("admin.name"))
            ->rules("required");
        $this->html("
        <script>
            function save(name, data) {
                let urlObject = window.URL || window.webkitURL || window;
                let export_blob = new Blob([data]);
                let save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a')
                save_link.href = urlObject.createObjectURL(export_blob);
                save_link.download = name;
                let event = document.createEvent('MouseEvents');
                event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                save_link.dispatchEvent(event);
            }
            $(document).ready(function() {
                let type = $.cookie('type');
                $.removeCookie('type');
                // private key
                let key = $.cookie('key');
                if (key) save('id_' + atob(type) + '.key', atob(key));
                $.removeCookie('key');
                // public key
                let pub_key = $.cookie('pub_key');
                if (pub_key) save('id_' + atob(type) + '.pub', atob(pub_key));
                $.removeCookie('pub_key');
            });
        </script>
        ");
    }

    /**
     * The data of the form.
     *
     * @return  array $data
     */
    public function data(): array
    {
        return [];
    }
}