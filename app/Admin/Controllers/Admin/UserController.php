<?php

namespace App\Admin\Controllers\Admin;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Admin\RoleModel;
use App\Admin\Models\Admin\UserRoleModel;
use App\Admin\Models\Model;
use App\Admin\Models\Admin\UserModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller 
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $form
            ->text('id')
            ->label(trans('admin.user.id'));
        $form
            ->text('username')
            ->label(trans('admin.user.username'));
        $form
            ->text('name')
            ->label(trans('admin.user.name'));
        $form
            ->dateRange('created_time')
            ->label(trans('admin.time.created.at'));
        $form
            ->dateRange('updated_time')
            ->label(trans('admin.time.updated.at'));

        $header = [
            (new Header())->field('id')->title(trans('admin.user.id'))->align()->width(32),
            (new Header())->field('username')->title(trans('admin.user.username'))->align()->width(160),
            (new Header())->field('name')->title(trans('admin.user.name'))->align()->width(160),
            (new Header())->field('avatar')->title(trans('admin.user.avatar'))->align()->width(32)->templet('image'),
            (new Header())->field('roles')->title(trans('admin.user.roles'))->align()->templet('tags', 'name'),
            (new Header())->field(Model::CREATED_AT)->title(trans('admin.time.created.at'))->align()->width(160),
            (new Header())->field(Model::UPDATED_AT)->title(trans('admin.time.updated.at'))->align()->width(160),
            (new Header())->field('')->title(trans('admin.table.operate'))->align()->width(220)->toolbar(),
        ];

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = UserModel::getPage($page, $perPage, $input);
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
            ->text('username')
            ->label(trans('admin.user.username'))
            ->required();
        $form
            ->text('password')
            ->label(trans('admin.user.password'))
            ->required();
        $form
            ->text('name')
            ->label(trans('admin.user.name'))
            ->required();
        $form
            ->image('avatar')
            ->label(trans('admin.user.avatar'))
            ->required();

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

        DB::beginTransaction();
        try {
            $attributes = [
                'username' => $request->input('username'),
                'password' => Hash::make($request->input('password')),
                'name' => $request->input('name'),
                'avatar' => $request->input('avatar'),
            ];

            $user = UserModel::withOnly([])->create($attributes);

            $userRoles = $roles
                ->map(function($role) use ($user) { 
                    return ['user_id' => $user->id, 'role_id' => $role->id, Model::DELETED_AT => 0]; 
                })
                ->toArray();

            UserRoleModel::upsert($userRoles, ['user_id', 'role_id']);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function show(Request $request, int $id)
    {
        $data = UserModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.user.id'))
            ->value($data->id);
        $form
            ->display('username')
            ->label(trans('admin.user.username'))
            ->value($data->username);
        $form
            ->display('password')
            ->label(trans('admin.user.password'))
            ->value($data->password);
        $form
            ->display('name')
            ->label(trans('admin.user.name'))
            ->value($data->name);
        $form
            ->image('avatar')
            ->label(trans('admin.user.avatar'))
            ->value($data->avatar);

        $roles = $form
            ->tags('roles')
            ->label(trans('admin.user.roles'));

        foreach($data->roles as $role) {
            $roles->tag()->value($role->name);
        }

        return $form->name(trans('admin.form.show'))->build();
    }

    public function edit(Request $request, int $id)
    {
        $data = UserModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.user.id'))
            ->value($data->id)
            ->required();
        $form
            ->text('username')
            ->label(trans('admin.user.username'))
            ->value($data->username)
            ->required();
        $form
            ->text('password')
            ->label(trans('admin.user.password'))
            ->value($data->password)
            ->required();
        $form
            ->text('name')
            ->label(trans('admin.user.name'))
            ->value($data->name)
            ->required();
        $form
            ->image('avatar')
            ->label(trans('admin.user.avatar'))
            ->value($data->avatar)
            ->required();

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
        $user = UserModel::withOnly(['roles'])->find($id);

        $roles = $request->input('roles') ?? [];
        $roles = RoleModel::findMany($roles);

        DB::beginTransaction();
        try {
            $attributes = [
                'password' => Hash::make($request->input('password')),
                'name' => $request->input('name'),
                'avatar' => $request->input('avatar'),
            ];

            UserModel::where('id', '=', $id)->update($attributes);

            $roleCreate = $roles
                ->filter(function($role) use ($user) {
                    return is_null($user->roles->find($role->id));
                })
                ->map(function($role) use ($user) {
                    return ['user_id' => $user->id, 'role_id' => $role->id, Model::DELETED_AT => 0];
                })
                ->toArray();

            UserRoleModel::upsert($roleCreate, ['user_id', 'role_id']);

            $roleDestroy = $user
                ->roles
                ->filter(function($role) use ($roles) {
                    return is_null($roles->find($role->id));
                })
                ->map(function($role) {
                    return $role->pivot->id;
                })
                ->toArray();
    
            UserRoleModel::destroy($roleDestroy);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request, int $id)
    {
        UserModel::destroy($id);

        return ['code' => 0, 'msg' => ''];
    }
}
