<?php

namespace App\Admin\Forms\AssistantForms;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Widgets\Form;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
     * @return  RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $type = $request->input('type', '');
        $passphrase = $request->input('passphrase', '');
        $passphrase_again = $request->input('passphrase_again', '');
        if ($passphrase != $passphrase_again) {
            admin_error(trans("admin.password") . trans("admin.mismatch"));
            return back();
        }
        $name = $request->input('name', '');
        // path
        $path = storage_path("app/admin/keys/");
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        // file
        $file = storage_path("app/admin/keys/id_$type");
        $pub_file = storage_path("app/admin/keys/id_$type.pub");
        // remove old key
        if (file_exists($file)) {
            unlink($file);
        }
        // remove old pub key
        if (file_exists($pub_file)) {
            unlink($pub_file);
        }
        // generate
        switch ($type) {
            case "rsa":
            case "ed25519":
                $process = new Process(['ssh-keygen', '-q', '-t', $type, '-b', '4096', '-f', $file, '-N', $passphrase, '-C', $name]);
                break;
            case "ecdsa":
                $process = new Process(['ssh-keygen', '-q', '-t', $type, '-b', '384', '-f', $file, '-N', $passphrase, '-C', $name]);
                break;
            case "dsa":
                $process = new Process(['ssh-keygen', '-q', '-t', $type, '-b', '1024', '-f', $file, '-N', $passphrase, '-C', $name]);
                break;
            default:
                admin_error("Unknown Key Type: $type");
                return back();
        }
        $process->run();
        // result
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $result = $process->getOutput();
        if (!empty($result)) {
            throw new ProcessFailedException($process);
        }
        $key = file_get_contents($file);
        $pub_key = file_get_contents($pub_file);
        $id = DB::table("ssh_key")->insertGetId([
            "username" => Auth::user()->name, 
            "type" => $type, 
            "passphrase" => $passphrase, 
            "name" => $name,
            "key" => $key,
            "pub_key" => $pub_key
        ]);
        // pass to session
        Session::put("id", $id);
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
            "ecdsa" => "ecdsa",
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
        $list = DB::table("ssh_key")
            ->where("id", "=", Session::remove("id"))
            ->get(["type", "key", "pub_key"]);
        $data = isset($list[0]) ? (array)$list[0] : ["type" => "", "key" => "", "pub_key" => ""];
        $type = str_replace("\n", "\\n", $data['type']);
        $key = str_replace("\n", "\\n", $data['key']);
        $pub_key = str_replace("\n", "\\n", $data['pub_key']);
        $this->html("
            <script>
                (function() {
                    // key type
                    const type = '$type'
                    // private key
                    const key = '$key';
                    if (key) save('id_' + type, key);
                    // public key
                    const pub_key = '$pub_key'
                    if (pub_key) save('id_' + type + '.pub', pub_key);
                })();
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
        // scroll to top
        $this->html("<script>document.querySelector('#pjax-container').scroll(0, 0);</script>");
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