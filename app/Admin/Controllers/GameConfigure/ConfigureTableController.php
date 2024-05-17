<?php

namespace App\Admin\Controllers\GameConfigure;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Model;
use App\Admin\Models\GameConfigure\ConfigureTableModel;
use App\Admin\Services\Extend\ServerService;
use Illuminate\Http\Request;

class ConfigureTableController extends Controller
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $form
            ->text('table_schema')
            ->label(trans('admin.database.schema'));
        $form
            ->text('table_name')
            ->label(trans('admin.database.table'));
        $form
            ->text('table_comment')
            ->label(trans('admin.database.comment'));
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
            (new Header())->field('table_schema')->title(trans('admin.database.schema'))->align()->width(160),
            (new Header())->field('table_name')->title(trans('admin.database.table'))->align()->width(160),
            (new Header())->field('table_comment')->title(trans('admin.database.comment'))->align()->width(160),
            (new Header())->field('name')->title(trans('admin.user.name'))->align()->width(160),
            (new Header())->field('state')->title(trans('admin.statistic.state'))->align(),
            (new Header())->field(Model::CREATED_AT)->title(trans('admin.time.created.at'))->align()->width(160),
            (new Header())->field(Model::UPDATED_AT)->title(trans('admin.time.updated.at'))->align()->width(160),
        ];

        $server = ServerService::getServer();
        if($server->tag == 'ALL') {
            return admin_view(config('admin.vendor.view.error'));
        }
        ConfigureTableModel::reload($server);

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = ConfigureTableModel::getPage($page, $perPage, $input);
        
        $data = $paginator->items();

        $paginate = (new Page())
            ->total($paginator->total())
            ->current($page)
            ->limit($perPage);

        return (new Table())
            ->form($form)
            ->header($header)
            ->data($data)
            ->right(['search'])
            ->paginate($paginate)
            ->build();
    }
}
