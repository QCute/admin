<?php

namespace App\Admin\Controllers\GameData;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\GameData\TableDataModel;
use App\Admin\Services\Extend\DatabaseService;
use App\Admin\Services\Extend\ServerService;
use App\Admin\Traits\FormOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TableDataController extends Controller
{
    use FormOutput;

    public function index(Request $request, string $table, string $action = '')
    {
        // [$table, ] = array_reverse(explode('/', $request->path()));\
        if($action !== '') return $this->{$action}($request, $table);

        $form = new Form();
        $form->hide();

        $server = ServerService::getServer();
        if($server->tag == 'ALL') {
            return admin_view(config('admin.vendor.view.error'));
        }

        $connection = DatabaseService::changeConnection($server);
        $columns = Schema::setConnection($connection)->getColumns($table);

        $columns = collect($columns)->map(function($item) { return (object)$item; });

        $this->buildIndex($form, $columns);

        $header = $columns->map(function($item) {
            return (new Header())->field($item->name)->title($item->comment ?? $item->name)->align()->minWidth(120);
        });

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);

        $paginator = TableDataModel::getPage($table, $page, $perPage, $input);
        
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
            ->paginate($paginate)
            ->build();
    }

    public function export(Request $request, string $table)
    {
        $filename = $table . '.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . '"' . $filename . '"',
        ];

        $server = ServerService::getServer();
        $columns = Schema::setConnection(DatabaseService::changeConnection($server))->getColumns($table);
        $columns = collect($columns)->pluck('comment')->toArray();

        return response()->streamDownload(function() use ($request, $table, $columns) {
            $handle = fopen('php://output', 'w');

            // fwrite($handle, chr(0xEF).chr(0xBB).chr(0xBF)); 
            // 导出的CSV文件是无BOM编码UTF-8，而我们通常使用UTF-8编码格式都是有BOM的。所以添加BOM于CSV中

            // Write title
            fputcsv($handle, $columns);

            $page = 1;
            $perPage = 100 * 1000;
            $input = $request->except(['_token', 'page', 'perPage']);

            while(true) {
                $paginator = TableDataModel::getSimplePage($table, $page, $perPage, $input);

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
