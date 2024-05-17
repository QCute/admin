<?php

namespace App\Admin\Controllers\Admin;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Model;
use App\Admin\Models\Admin\PermissionModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller 
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $form
            ->text('id')
            ->label(trans('admin.permission.id'));
        $form
            ->text('name')
            ->label(trans('admin.permission.name'));
        $form
            ->text('tag')
            ->label(trans('admin.permission.tag'));
        $form
            ->text('http_method')
            ->label(trans('admin.permission.http_method'));
        $form
            ->text('http_path')
            ->label(trans('admin.permission.http_path'));
        $form
            ->dateRange('created_time')
            ->label(trans('admin.time.created.at'));
        $form
            ->dateRange('updated_time')
            ->label(trans('admin.time.updated.at'));

        $header = [
            (new Header())->field('id')->title(trans('admin.permission.id'))->align()->width(32),
            (new Header())->field('name')->title(trans('admin.permission.name'))->align()->width(160),
            (new Header())->field('tag')->title(trans('admin.permission.tag'))->align()->width(width: 160)->templet('tag'),
            (new Header())->field('http_method')->title(trans('admin.permission.http_method'))->width(width: 160)->align()->templet('tags', ','),
            (new Header())->field('http_path')->title(trans('admin.permission.http_path'))->align()->templet('tags', "\n"),
            (new Header())->field(Model::CREATED_AT)->title(trans('admin.time.created.at'))->align()->width(160),
            (new Header())->field(Model::UPDATED_AT)->title(trans('admin.time.updated.at'))->align()->width(160),
            (new Header())->field('')->title(trans('admin.table.operate'))->align()->width(220)->toolbar(),
        ];

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = PermissionModel::getPage($page, $perPage, $input);
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
            ->label(trans('admin.permission.name'));
        $form
            ->text('tag')
            ->label(trans('admin.permission.tag'));
        $form
            ->text('http_method')
            ->label(trans('admin.permission.http_method'));
        $form
            ->text('http_path')
            ->label(trans('admin.permission.http_path'));

        return $form->name(trans('admin.form.create'))->build();
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $attributes = $request->except('_token');

            PermissionModel::create($attributes);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function show(Request $request, int $id)
    {
        $data = PermissionModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.permission.id'))
            ->value($data->id);
        $form
            ->display('name')
            ->label(trans('admin.permission.name'))
            ->value($data->name);
        $form
            ->display('tag')
            ->label(trans('admin.permission.tag'))
            ->value($data->tag);
        $form
            ->display('http_method')
            ->label(trans('admin.permission.http_method'))
            ->value($data->http_method);
        $form
            ->display('http_path')
            ->label(trans('admin.permission.http_path'))
            ->value($data->http_path);

        return $form->name(trans('admin.form.show'))->build();
    }

    public function edit(Request $request, int $id)
    {
        $data = PermissionModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.permission.id'))
            ->value($data->id)
            ->required();
        $form
            ->text('name')
            ->label(trans('admin.permission.name'))
            ->value($data->name)
            ->required();
        $form
            ->text('tag')
            ->label(trans('admin.permission.tag'))
            ->value($data->tag)
            ->required();
        $form
            ->text('http_method')
            ->label(trans('admin.permission.http_method'))
            ->value($data->http_method)
            ->required();
        $form
            ->image('http_path')
            ->label(trans('admin.permission.http_path'))
            ->value($data->http_path)
            ->required();

        return $form->name(trans('admin.form.edit'))->method('PATCH')->build();
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        try {
            $attributes = $request->except('_token');

            PermissionModel::where('id', '=', $id)->update($attributes);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request, int $id)
    {
        PermissionModel::destroy($id);

        return ['code' => 0, 'msg' => ''];
    }
}
