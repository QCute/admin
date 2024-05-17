<?php

namespace App\Admin\Controllers\Admin;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Admin\MenuModel;
use App\Admin\Models\Model;
use App\Admin\Services\Auth\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller 
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $form
            ->text('id')
            ->label(trans('admin.menu.id'));
        $form
            ->text('parent_id')
            ->label(trans('admin.menu.parent_id'));
        $form
            ->text('type')
            ->label(trans('admin.menu.type'));
        $form
            ->text('order')
            ->label(trans('admin.menu.order'));
        $form
            ->text('title')
            ->label(trans('admin.menu.title'));
        $form
            ->text('icon')
            ->label(trans('admin.menu.icon'));
        $form
            ->text('url')
            ->label(trans('admin.menu.url'));
        $form
            ->dateRange('created_time')
            ->label(trans('admin.time.created.at'));
        $form
            ->dateRange('updated_time')
            ->label(trans('admin.time.updated.at'));

        $header = [
            (new Header())->field('id')->title(trans('admin.menu.id'))->align()->width(32),
            (new Header())->field('parent_id')->title(trans('admin.menu.parent_id'))->align()->width(160),
            (new Header())->field('type')->title(trans('admin.menu.type'))->align()->width(160),
            (new Header())->field('order')->title(trans('admin.menu.order'))->align()->width(160),
            (new Header())->field('title')->title(trans('admin.menu.title'))->align()->width(160),
            (new Header())->field('icon')->title(trans('admin.menu.icon'))->align()->width(160)->templet('icon'),
            (new Header())->field('url')->title(trans('admin.menu.url'))->align(),
            (new Header())->field(Model::CREATED_AT)->title(trans('admin.time.created.at'))->align()->width(160),
            (new Header())->field(Model::UPDATED_AT)->title(trans('admin.time.updated.at'))->align()->width(160),
            (new Header())->field('')->title(trans('admin.table.operate'))->align()->width(220)->toolbar(),
        ];

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = MenuModel::getPage($page, $perPage, $input);
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

        $user = AuthService::user();

        $menus = MenuModel::getMenus($user);

        $select = $form
            ->select('parent_id')
            ->label(trans('admin.menu.parent_id'))
            ->required();
        $select->option()->label(trans('admin.menu.path.root'))->value(0);
        foreach($menus as $menu) {
            $select->option()->label($menu['title'])->value($menu['id']);
        }

        $types = [
            [
                'name' => trans('admin.menu.type.menu'),
                'value' => 1,
            ],
            [
                'name' => trans('admin.menu.type.directory'),
                'value' => 0,
            ]
        ];
        $select = $form
            ->select('type')
            ->label(trans('admin.menu.type'))
            ->required();
        foreach($types as $type) {
            $select->option()->label($type['name'])->value($type['value']);
        }

        $form
            ->text('order')
            ->label(trans('admin.menu.order'))
            ->required();
        $form
            ->text('title')
            ->label(trans('admin.menu.title'))
            ->required();
        $form
            ->iconPicker('icon')
            ->label(trans('admin.menu.icon'))
            ->required();
        $form
            ->text('url')
            ->label(trans('admin.menu.url'))
            ->required();

        return $form->name(trans('admin.form.create'))->build();
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $attributes = $request->except('_token');

            MenuModel::create($attributes);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function show(Request $request, int $id)
    {
        $data = MenuModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('parent_id')
            ->label(trans('admin.menu.parent_id'))
            ->value($data->parent_id);
        $form
            ->display('type')
            ->label(trans('admin.menu.type'))
            ->value($data->type);
        $form
            ->display('order')
            ->label(trans('admin.menu.order'))
            ->value($data->order);
        $form
            ->display('title')
            ->label(trans('admin.menu.title'))
            ->value($data->title);
        $form
            ->icon('icon')
            ->label(trans('admin.menu.icon'))
            ->value($data->icon);
        $form
            ->display('url')
            ->label(trans('admin.menu.url'))
            ->value($data->url);

        return $form->name(trans('admin.form.show'))->build();
    }

    public function edit(Request $request, int $id)
    {
        $data = MenuModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.menu.id'))
            ->value($data->id);
        $user = AuthService::user();

        $menus = MenuModel::getMenus($user);

        $select = $form
            ->select('parent_id')
            ->label(trans('admin.menu.parent_id'))
            ->required();
        $select->option()->label(trans('admin.menu.path.root'))->value(0);
        foreach($menus as $menu) {
            $select->option()->label($menu['title'])->value($menu['id'])->select($data->parent_id == $menu['id']);    
        }

        $types = [
            [
                'name' => trans('admin.menu.type.menu'),
                'value' => 1,
            ],
            [
                'name' => trans('admin.menu.type.directory'),
                'value' => 0,
            ]
        ];
        $select = $form
            ->select('type')
            ->label(trans('admin.menu.type'))
            ->required();
        foreach($types as $type) {
            $select->option()->label($type['name'])->value($type['value'])->select($data->type == $type['value']);
        }

        $form
            ->text('order')
            ->label(trans('admin.menu.order'))
            ->value($data->order);
        $form
            ->text('title')
            ->label(trans('admin.menu.title'))
            ->value($data->title);
        $form
            ->iconPicker('icon')
            ->label(trans('admin.menu.icon'))
            ->value($data->icon);
        $form
            ->text('url')
            ->label(trans('admin.menu.url'))
            ->value($data->url);

        return $form->name(trans('admin.form.edit'))->method('PATCH')->build();
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            $attributes = $request->except('_token');

            MenuModel::where('id', '=', $id)->update($attributes);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request, int $id)
    {
        MenuModel::destroy($id);

        return ['code' => 0, 'msg' => ''];
    }
}
