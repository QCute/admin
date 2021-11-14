<?php

namespace App\Admin\Forms\AssistantForms;

use Exception;

use Symfony\Component\Process\Process;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
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
        // path
        $path = storage_path("app/admin/keys/");
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
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
        // pass to session
        Session::put("key-type", base64_encode($type));
        Session::put("key", base64_encode(file_get_contents($file)));
        Session::put("pub-key", base64_encode(file_get_contents($pub_file)));
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
        // data
        $type = Session::remove("key-type");
        $key = Session::remove("key");
        $pub_key = Session::remove("pub-key");
        $this->html("
        <script>
            $(document).ready(function() {
                // key type
                const type = '{$type}'
                // private key
                const key = '{$key}';
                if (key) save('id_' + atob(type), atob(key));
                // public key
                const pub_key = '{$pub_key}'
                if (pub_key) save('id_' + atob(type) + '.pub', atob(pub_key));
            });
            function save(name, data) {
                const aTag = document.createElement('a');
                aTag.setAttribute('download', name);
                const blob = new Blob([data], { 'type': 'application/octet-stream' });
                aTag.setAttribute('href', URL.createObjectURL(blob));
                aTag.click();
                URL.revokeObjectURL(blob);
            }
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