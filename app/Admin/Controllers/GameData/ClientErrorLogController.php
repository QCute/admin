<?php

namespace App\Admin\Controllers\GameData;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\GameData\ClientErrorLogModel;
use App\Admin\Models\Model;
use App\Admin\Traits\FormOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClientErrorLogController extends Controller
{
    use FormOutput;

    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $columns = Schema::connection('api')->getColumns(config('api.database.client_error_log_table'));

        $columns = collect($columns)->map(function($item) { return (object)$item; });

        $this->buildIndex($form, $columns);

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
        $paginator = ClientErrorLogModel::getPage($page, $perPage, $input);

        $data = $paginator->items();

        $paginate = (new Page())
            ->total($paginator->total())
            ->current($page)
            ->limit($perPage);

        return (new Table())
            ->form($form)
            ->header($header)
            ->data($data)
            ->right(['filter', 'download', 'export', 'search'])
            ->operation(['show', 'delete'])
            ->paginate($paginate)
            ->build();
    }

    public function show(Request $request, int $id)
    {
        $data = ClientErrorLogModel::find($id);

        $form = new Form();

        $columns = Schema::connection('api')->getColumns(config('api.database.client_error_log_table'));

        $form = $this->buildShow($form, $columns, $data);

        return $form->name(trans('admin.form.show'))->build();
    }

    public function destroy(Request $request, int $id)
    {
        ClientErrorLogModel::destroy($id);

        return ['code' => 0, 'msg' => ''];
    }
}
