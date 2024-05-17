<?php

namespace App\Admin\Controllers\Operation;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Model;
use App\Admin\Models\Operation\UserManageModel;
use App\Admin\Services\Auth\AuthService;
use App\Admin\Services\Extend\ChannelService;
use App\Admin\Services\Extend\MachineService;
use App\Admin\Services\Extend\ServerService;
use App\Admin\Traits\FormOutput;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserManageController extends Controller
{
    use FormOutput;

    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.user_manage_table'));

        $columns = collect($columns)->map(function($item) { return (object)$item; });

        $form = $this->buildIndex($form, $columns);

        $header = $columns
            ->filter(function($item) {
                return $item->name != Model::DELETED_AT;
            })
            ->map(function($item) {
                return (new Header())->field($item->name)->title($item->comment ?? $item->name)->align()->minWidth(160);
            });

        $header->push((new Header())->field('')->title(trans('admin.table.operate'))->align()->width(160)->toolbar());

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = UserManageModel::getPage($page, $perPage, $input);
        $data = $paginator->items();

        $paginate = (new Page())
            ->total($paginator->total())
            ->current($page)
            ->limit($perPage);

        return (new Table())
            ->form($form)
            ->header($header)
            ->data($data)
            ->right(['filter', 'create', 'download', 'export', 'search'])
            ->operation(['show', 'delete'])
            ->paginate($paginate)
            ->build();
    }

    public function create(Request $request)
    {
        $form = new Form();
        $form->name(trans('admin.form.create'));

        $select = $form->select('server')->label(trans('admin.server'))->required();
        $options = [
            'SERVER' => trans('admin.current.server'),
            'CHANNEL' => trans('admin.current.channel'),
            'ALL' => trans('admin.all'),
        ];
        foreach($options as $option => $name) {
            $select->option()->label($name)->value($option);
        }

        $form->textArea('role')->label(trans('admin.role'));

        $radio = $form->radio('operation')->label(trans('admin.user.status'))->required();
        $options = [
            'ban' => trans('admin.user.status.ban'),
            'normal' => trans('admin.user.status.normal'),
            'insider' => trans('admin.user.status.insider'),
            'master' => trans('admin.user.status.master'),
        ];
        foreach($options as $option => $name) {
            $radio->option()->label($name)->value($option);
        }

        $form->dateTime('time')->label(trans('admin.time'))->required();

        return $form->build();
    }

    public function store(Request $request)
    {
        $server = $request->input('server');

        $roles = collect(explode("\n", $request->input('role')))
            ->map(function($role) { 
                return trim($role);
            })
            ->filter(function($role) {
                return $role !== '';
            })
            ->map(function($role) {
                return intval($role); 
            });

        $operation = $request->input('operation');

        try {

            $servers = [];
            switch($server) {
                case 'ALL': {
                    $servers = ServerService::getServers();
                };break;
                case 'CHANNEL': {
                    $servers = ChannelService::getChannel()->servers;
                };break;
                case 'SERVER': {
                    $servers = [ServerService::getServer()];
                    if($servers[0]->tag == 'ALL') {
                        return ['code' => 1, 'msg' => trans('admin.server.invalid')];
                    }
                };break;
                default: {
                    return ['code' => 1, 'msg' => trans('admin.server.invalid'), 'server' => $server];
                }
            }

            $command = ($roles->isEmpty() ? 'server' : 'role') . '/' . 'login' .  '/' . $operation;
            $data = [
                'roles' => $roles
            ];

            $result = MachineService::send($server, $command, $data);

            if($result['error'] || $result['failed']) {
                return ['code' => 1, 'msg' => trans('admin.status.failure'), 'failed' => $result['failed'], 'error' => $result['error']];
            }

            $attributes = [
                'user_id' => AuthService::user()->id,
                'server' => json_encode($servers),
                'roles' => json_encode($roles),
                'operation' => $operation,
            ];
    
            UserManageModel::create($attributes);

        } catch (Exception $e) {
            return ['code' => $e->getCode(), 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request)
    {
        return ['code' => 0, 'msg' => ''];
    }
}
