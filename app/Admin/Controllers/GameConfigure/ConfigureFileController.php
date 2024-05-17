<?php

namespace App\Admin\Controllers\GameConfigure;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Model;
use App\Admin\Models\GameConfigure\ConfigureFileModel;
use App\Admin\Services\Extend\ServerService;
use Illuminate\Http\Request;

class ConfigureFileController extends Controller
{
    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $form
            ->text('file')
            ->label(trans('admin.file'));
        $form
            ->text('comment')
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
            (new Header())->field('file')->title(trans('admin.file'))->align()->width(320),
            (new Header())->field('comment')->title(trans('admin.database.comment'))->align()->width(160),
            (new Header())->field('name')->title(trans('admin.user.name'))->align()->width(160),
            (new Header())->field('state')->title(trans('admin.statistic.state'))->align(),
            (new Header())->field('cvs')->title('CVS')->align(),
            (new Header())->field(Model::CREATED_AT)->title(trans('admin.time.created.at'))->align()->width(160),
            (new Header())->field(Model::UPDATED_AT)->title(trans('admin.time.updated.at'))->align()->width(160),
        ];

        [$file, ] = array_reverse(explode('/', $request->path()));
        $server = ServerService::getServer();
        if($server->tag == 'ALL') {
            return admin_view(config('admin.vendor.view.error'));
        }
        ConfigureFileModel::reload($server, $file);

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = ConfigureFileModel::getPage($file, $page, $perPage, $input);

        $paginate = (new Page())
            ->total($paginator->total())
            ->current($page)
            ->limit($perPage);

        $data = $paginator->items();

        return (new Table())
            ->form($form)
            ->header($header)
            ->data($data)
            ->right(['search'])
            ->paginate($paginate)
            ->build();
    }
}
