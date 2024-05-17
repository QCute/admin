<?php


namespace App\Admin\Controllers\GameData;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Action;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\GameData\TableListModel;
use App\Admin\Services\Extend\ServerService;
use Illuminate\Http\Request;

class TableListController extends Controller
{
    public function index(Request $request, string $type, string $action = '')
    {
        // [$type, ] = array_reverse(explode('/', $request->path()));
        if($action !== '') return $this->{$action}($request, $type);

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

        $header = [
            (new Header())->field('TABLE_SCHEMA')->title(trans('admin.database.schema'))->align()->width(160),
            (new Header())->field('TABLE_NAME')->title(trans('admin.database.table'))->align(),
            (new Header())->field('TABLE_COMMENT')->title(trans('admin.database.comment'))->align(),
            (new Header())->field('TABLE_ROWS')->title(trans('admin.database.row'))->align()->width(160),
            (new Header())->field('')->title(trans('admin.table.operate'))->align()->width(80)->toolbar(),
        ];

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);

        $server = ServerService::getServer();
        if($server->tag == 'ALL') {
            return admin_view(config('admin.vendor.view.error'));
        }
        $paginator = TableListModel::getPage($server, $type, $page, $perPage, $input);

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
            ->operation([
                (new Action())->title(trans('admin.table.show'))->color('green')->icon('show')->event('CHANGE')->value(admin_path('/game-data'))
            ])
            ->paginate($paginate)
            ->build();
    }

    public function export(Request $request, string $type)
    {
        $filename = $type . '_' . 'table.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . '"' . $filename . '"',
        ];

        return response()->streamDownload(function() use ($request, $type) {
            $handle = fopen('php://output', 'w');

            // fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF)); 
            // 导出的CSV文件是无BOM编码UTF-8，而我们通常使用UTF-8编码格式都是有BOM的。所以添加BOM于CSV中

            // Write title
            // fputcsv($handle, $titles);

            $page = 1;
            $perPage = 100 * 1000;
            $input = $request->except(['_token', 'page', 'perPage']);

            $server = ServerService::getServer();
            while(true) {
                $paginator = TableListModel::getSimplePage($server, $type, $page, $perPage, $input);

                foreach($paginator->items() as $row) {
                    fputcsv($handle, $row->toArray());
                }

                if($paginator->isEmpty()) break;

                $page++;
            }

            fclose($handle);

        }, $filename, $headers);
    }
}
