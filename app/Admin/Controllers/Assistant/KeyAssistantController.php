<?php

namespace App\Admin\Controllers\Assistant;

use App\Admin\Builders\Form;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Assistant\SSHKeyModel;
use App\Admin\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class KeyAssistantController extends Controller
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->name(trans('admin.form.create'));

        $select = $form->select('type')->label(trans('admin.type'))->required();
        $options = [
            'ED25519' => 'ED25519',
            'ECDSA' => 'ECDSA',
            'DSA' => 'DSA',
            'RSA' => 'RSA',
        ];
        foreach($options as $name => $option) {
            $select->option()->value($name)->label($option);
        }

        $form->text('name')->label(trans('admin.form.name'))->required();

        $form->password('passowrd')->label(trans('admin.form.password'))->required();

        $form->password('again')->label(trans('admin.form.password'))->required();

        return $form->build();
    }

    public function submit(Request $request)
    {
        $type = $request->input('type', '');
        if($type === '') {
            return back()->withErrors('');
        }

        $name = $request->input('name', '');
        if ($name === '') {
            return back()->withErrors('');
        }

        $passowrd = $request->input('passowrd', '');
        if($passowrd === '') {
            return back()->withErrors('');
        }

        $again = $request->input('again', '');
        if ($passowrd != $again) {
            return back()->withErrors('');
        }

        // path
        $path = storage_path('app/admin/keys/');
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
            case 'RSA':
            case 'ED25519':
                $process = new Process(['ssh-keygen', '-q', '-t', $type, '-b', '4096', '-f', $file, '-N', $passowrd, '-C', $name]);
                break;
            case 'ECDSA':
                $process = new Process(['ssh-keygen', '-q', '-t', $type, '-b', '384', '-f', $file, '-N', $passowrd, '-C', $name]);
                break;
            case 'DSA':
                $process = new Process(['ssh-keygen', '-q', '-t', $type, '-b', '1024', '-f', $file, '-N', $passowrd, '-C', $name]);
                break;
            default:
                return back()->withErrors('');
        }
        $process->run();

        // result
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $result = $process->getOutput();
        if (!$result === '') {
            throw new ProcessFailedException($process);
        }

        $key = file_get_contents($file);
        $pub_key = file_get_contents($pub_file);
        $data = [
            'username' => AuthService::user()->name, 
            'type' => $type,
            'passphrase' => $passowrd,
            'name' => $name,
            'key' => $key,
            'pub_key' => $pub_key
        ];

        SSHKeyModel::create($data);

        return back()->with('file', ['key' => $key, 'pub' => $pub_key]);
    }
}
