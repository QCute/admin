<?php

namespace App\Admin\Controllers\Admin;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Model;
use App\Admin\Models\Admin\OperationLogModel;
use Illuminate\Http\Request;

class OperationLogController extends Controller 
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $form
            ->text('id')
            ->label(trans('admin.id'));
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
            (new Header())->field('id')->title(trans('admin.id'))->align()->width(32),
            (new Header())->field('user_id')->title(trans('admin.user.id'))->align()->width(160),
            (new Header())->field('path')->title(trans('admin.log.path'))->align()->width(160),
            (new Header())->field('method')->title(trans('admin.log.method'))->align(),
            (new Header())->field('ip')->title(trans('admin.log.ip'))->align(),
            (new Header())->field('input')->title(trans('admin.log.input'))->align(),
            (new Header())->field(Model::CREATED_AT)->title(trans('admin.time.created.at'))->align()->width(160),
            (new Header())->field(Model::UPDATED_AT)->title(trans('admin.time.updated.at'))->align()->width(160),
            (new Header())->field('')->title(trans('admin.table.operate'))->align()->width(220)->toolbar(),
        ];

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = OperationLogModel::getPage($page, $perPage, $input);
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
            ->operation(['show'])
            ->paginate($paginate)
            ->build();
    }

    public function show(Request $request, int $id)
    {
        $data = OperationLogModel::withOnly([])->find($id);

        $form = new Form();

        $form
            ->display('id')
            ->label(trans('admin.id'))
            ->value($data->id);
        $form
            ->display('user_id')
            ->label(trans('admin.user.id'))
            ->value($data->user_id);
        $form
            ->display('path')
            ->label(trans('admin.log.path'))
            ->value($data->path);
        $form
            ->display('method')
            ->label(trans('admin.log.method'))
            ->value($data->method);
        $form
            ->display('ip')
            ->label(trans('admin.log.ip'))
            ->value($data->ip);
        $form
            ->textArea('input')
            ->label(trans('admin.log.input'))
            ->value($data->input)
            ->disabled();

        return $form->name(trans('admin.form.show'))->build();
    }
}
