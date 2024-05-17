<?php

namespace App\Admin\Controllers\ServerManage;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Admin\RoleModel;
use App\Admin\Models\Extend\ChannelModel;
use App\Admin\Models\Extend\ChannelServerModel;
use App\Admin\Models\Extend\RoleChannelModel;
use App\Admin\Models\Model;
use App\Admin\Traits\FormOutput;
use App\Api\Models\ServerModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChannelListController extends Controller
{
    use FormOutput;

    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.channel_table'));

        $columns = collect($columns)->map(function($item) { return (object)$item; });

        $this->buildIndex($form, $columns);

        $header = $columns
            ->filter(function($item) {
                return $item->name != Model::DELETED_AT;
            })
            ->map(function($item) {
                return (new Header())->field($item->name)->title($item->comment ?? $item->name)->align()->minWidth(160);
            });

        $header->push((new Header())->field('')->title(trans('admin.table.operate'))->align()->width(220)->toolbar());

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = ChannelModel::getPage($page, $perPage, $input);
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
            ->operation(['show', 'edit', 'delete'])
            ->paginate($paginate)
            ->build();
    }

    public function create(Request $request)
    {
        $form = new Form();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.channel_table'));

        $form = $this->buildCreate($form, $columns);

        $check = $form
            ->checkBox('roles')
            ->label(trans('admin.role'))
            ->required();

        foreach (RoleModel::all() as $role) {
            $check->option()->label($role->name)->value($role->id);
        }

        return $form->name(trans('admin.form.create'))->build();
    }

    public function store(Request $request)
    {
        $roles = $request->input('roles') ?? [];
        $roles = RoleModel::findMany($roles);

        $attributes = $request->except(['_token', 'roles']);
        $attributes['permission'] = empty($roles) ? 'ROLE' : '';

        DB::beginTransaction();
        try {
            $channel = ChannelModel::create($attributes);

            $roles = collect($roles)
                ->map(function($role) use ($channel) {
                    return ['role_id' => $role->id, 'channel_id' => $channel->id, Model::DELETED_AT => 0];
                })
                ->toArray();

            RoleChannelModel::upsert($roles, ['role_id', 'channel_id']);

            // find the ALL tag server
            $server = ServerModel::where('tag', 'ALL')->first();
            $attributes = ['channel_id' => $channel->id, 'server_id' => $server->id];
            ChannelServerModel::create($attributes);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }

        return ['code' => 0, 'msg' => ''];
    }

    public function show(Request $request, int $id)
    {
        $data = ChannelModel::find($id);

        $form = new Form();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.channel_table'));

        $form = $this->buildShow($form, $columns, $data);

        $tags = $form->tags('tags')->label(trans('admin.role'));
        foreach($data->roles as $role) {
            $tags->tag()->value($role->name);
        }

        return $form->name(trans('admin.form.show'))->build();
    }

    public function edit(Request $request, int $id)
    {
        $data = ChannelModel::find($id);

        $form = new Form();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.channel_table'));

        $form = $this->buildEdit($form, $columns, $data);

        $check = $form
            ->checkBox('roles')
            ->label(trans('admin.role'))
            ->required();
        
        $roles = $data->roles->keyBy('id');
        foreach(RoleModel::all() as $role) {
            $checked = $roles->has($role->id);
            $check->option()->label($role->name)->value($role->id)->check($checked);
        }

        return $form->name(trans('admin.form.edit'))->method('PATCH')->build();
    }

    public function update(Request $request, int $id)
    {
        $channel = ChannelModel::find($id);

        $roles = $request->input('roles') ?? [];
        $roles = RoleModel::findMany($roles);

        DB::beginTransaction();
        try {
            $channel->permission = !empty($roles) ? 'ROLE' : '';
            $channel->save();

            $roleCreate = $roles
                ->filter(function($role) use ($channel) {
                    return is_null($channel->roles->find($role->id));
                })
                ->map(function($role) use ($channel) {
                    return ['role_id' => $role->id, 'channel_id' => $channel->id, Model::DELETED_AT => 0];
                })
                ->toArray();

            RoleChannelModel::upsert($roleCreate, ['role_id', 'channel_id']);

            $roleDestroy = $channel
                ->roles
                ->filter(function($role) use ($roles) {
                    return is_null($roles->find($role->id));
                })
                ->map(function($role) {
                    return $role->pivot->id;
                })
                ->toArray();

            RoleChannelModel::destroy($roleDestroy);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request, int $id)
    {
        ChannelModel::destroy($id);

        return ['code' => 0, 'msg' => ''];
    }
}
