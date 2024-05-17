<?php

namespace App\Admin\Controllers\Admin;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Admin\PermissionModel;
use App\Admin\Models\Admin\RoleModel;
use App\Admin\Models\Admin\RolePermissionModel;
use App\Admin\Models\Model;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller 
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $form
            ->text('id')
            ->label(trans('admin.id'));
        $form
                ->text('name')
                ->label(trans('admin.role.name'));
        $form
            ->text('tag')
            ->label(trans('admin.role.tag'));
        $form
            ->dateRange('created_time')
            ->label(trans('admin.time.created.at'));
        $form
            ->dateRange('updated_time')
            ->label(trans('admin.time.updated.at'));

        $header = [
            (new Header())->field('id')->title(trans('admin.role.id'))->align()->width(32),
            (new Header())->field('name')->title(trans('admin.role.name'))->align()->width(160),
            (new Header())->field('tag')->title(trans('admin.role.tag'))->align()->width(160),
            (new Header())->field('permissions')->title(trans('admin.role.permissions'))->align()->templet('tags', 'name'),
            (new Header())->field(Model::CREATED_AT)->title(trans('admin.time.created.at'))->align()->width(160),
            (new Header())->field(Model::UPDATED_AT)->title(trans('admin.time.updated.at'))->align()->width(160),
            (new Header())->field('')->title(trans('admin.table.operate'))->align()->width(220)->toolbar(),
        ];

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = RoleModel::getPage($page, $perPage, $input);
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

        $form
            ->text('name')
            ->label(trans('admin.role.name'))
            ->required();
        $form
            ->text('tag')
            ->label(trans('admin.role.tag'))
            ->required();

        $permissions = PermissionModel::all();

        $check = $form
            ->checkBox('permissions')
            ->label(trans('admin.role.permissions'))
            ->required();

        foreach (PermissionModel::all() as $permission) {
            $check->option()->label($permission->name)->value($permission->id);
        }

        return $form->name(trans('admin.form.create'))->build();
    }

    public function store(Request $request)
    {
        $permissions = $request->input('permissions') ?? [];
        $permissions = PermissionModel::findMany($permissions);

        DB::beginTransaction();
        try {
            $attributes = [
                'name' => $request->input('name'),
                'tag' => $request->input('tag'),
            ];

            $role = RoleModel::withOnly([])->create($attributes);

            $rolePermissions = $permissions
                ->map(function($permission) use ($role) { 
                    return ['role_id' => $role->id, 'permission_id' => $permission->id, Model::DELETED_AT => 0]; 
                })
                ->toArray();

            RolePermissionModel::update($rolePermissions, ['role_id', 'permission_id']);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function show(Request $request, int $id)
    {
        $data = RoleModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.role.id'))
            ->value($data->id);
        $form
            ->display('name')
            ->label(trans('admin.role.name'))
            ->value($data->name);
        $form
            ->display('tag')
            ->label(trans('admin.role.tag'))
            ->value($data->tag);

        $permissions = $form
            ->tags('permissions')
            ->label(trans('admin.role.permissions'));

        foreach($data->permissions as $permission) {
            $permissions->tag()->value($permission->name);
        }
    
        return $form->name(trans('admin.form.show'))->build();
    }

    public function edit(Request $request, int $id)
    {
        $data = RoleModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.role.id'))
            ->value($data->id)
            ->required();
        $form
            ->text('name')
            ->label(trans('admin.role.name'))
            ->value($data->name)
            ->required();
        $form
            ->text('tag')
            ->label(trans('admin.role.tag'))
            ->value($data->tag)
            ->required();

        $check = $form
            ->checkBox('permissions')
            ->label(trans('admin.role.permissions'))
            ->required();
        
        $permissions = $data->permissions->keyBy('id');
        foreach(PermissionModel::all() as $permission) {
            $checked = $permissions->has($permission->id);
            $check->option()->label($permission->name)->value($permission->id)->check($checked);
        }
    
        return $form->name(trans('admin.form.edit'))->method('PATCH')->build();
    }

    public function update(Request $request, int $id)
    {
        $role = RoleModel::withOnly(['permissions'])->find($id);

        $permissions = $request->input('permissions') ?? [];
        $permissions = PermissionModel::findMany($permissions);

        DB::beginTransaction();
        try {
            $attributes = [
                'name' => $request->input('name'),
                'avatar' => $request->input('tag'),
            ];

            RoleModel::withOnly([])->where('id', '=', $id)->update($attributes);

            $permissionCreate = $permissions
                ->filter(function($permission) use ($role) {
                    return is_null($role->permission->find($permission->id));
                })
                ->map(function($permission) use ($role) {
                    return ['role_id' => $role->id, 'permission_id' => $permission->id, Model::DELETED_AT => 0];
                })
                ->toArray();

            RolePermissionModel::update($permissionCreate, ['role_id', 'permission_id']);

            $permissionDestroy = $role
                ->permissions
                ->filter(function($permission) use ($permissions) {
                    return is_null($permissions->find($permission->id));
                })
                ->map(function($permission) {
                    return $permission->pivot->id;
                })
                ->toArray();

            RolePermissionModel::destroy($permissionDestroy);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request, int $id)
    {
        RoleModel::destroy($id);

        return ['code' => 0, 'msg' => ''];
    }
}
