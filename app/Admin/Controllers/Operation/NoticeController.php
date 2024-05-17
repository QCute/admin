<?php

namespace App\Admin\Controllers\Operation;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Model;
use App\Admin\Models\Operation\NoticeModel;
use App\Admin\Services\Auth\AuthService;
use App\Admin\Services\Extend\ChannelService;
use App\Admin\Services\Extend\MachineService;
use App\Admin\Services\Extend\ServerService;
use App\Admin\Traits\FormOutput;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NoticeController extends Controller
{
    use FormOutput;

    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.notice_table'));

        $columns = collect($columns)->map(function($item) { return (object)$item; });

        $form = $this->buildIndex($form, $columns);

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
        $paginator = NoticeModel::getPage($page, $perPage, $input);
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
            ->operation(['show', 'delete'])
            ->paginate($paginate)
            ->build();
    }

    public function create(Request $request)
    {
        $form = new Form();
        $form->name(trans('admin.form.create'));

        $select = $form->select('type')->label(trans('admin.server'))->required();
        $options = [
            'SERVER' => trans('admin.current.server'),
            'CHANNEL' => trans('admin.current.channel'),
            'ALL' => trans('admin.all'),
        ];
        foreach($options as $option => $name) {
            $select->option()->label($name)->value($option);
        }

        $select = $form->select('scope')->label(trans('admin.type'))->required();
        $options = [
            'board' => trans('admin.notice.type.board'),
            'mail' => trans('admin.notice.type.mail'),
            'chat' => trans('admin.notice.type.chat'),
            'float' => trans('admin.notice.type.float'),
            'scroll' => trans('admin.notice.type.scroll'),
            'pop' => trans('admin.notice.type.pop'),
            'dialog' => trans('admin.notice.type.dialog'),
        ];
        foreach($options as $option => $name) {
            $select->option()->label($name)->value($option);
        }

        $form->text('title')->label(trans('admin.form.title'))->required();

        $form->textArea('content')->label(trans('admin.form.content'))->required();

        $form->textArea('item')->label(trans('admin.form.item'))->required();

        return $form->build();
    }

    public function store(Request $request)
    {
        $server = $request->input('server');

        $title = $request->input('title');
        $content = $request->input('content');
        $item = collect(explode("\n", $request->input('item')))
            ->filter(function($item) {
                return $item !== '';
            })
            ->map(function($item) { 
                return collect(explode(",", trim($item)))
                    ->filter(function($item) {
                        return $item !== '';
                    })
                    ->reduce(function($acc, $item) {
                        [$head, $tail] = explode("=", trim($item));
                        $head = trim($head);
                        $tail = trim($tail);
                        
                        if($head === '') {
                            return $acc;
                        }
                        
                        if($tail === '') {
                            return $acc;
                        }

                        $acc[$head] = trim($tail);
                        return $acc;
                    }, []);
            });

        try {
            $servers = [];
            switch($server) {
                case 'ALL': {
                    $servers = ServerService::getServers();
                };break;
                case 'CHANNEL': {
                    $servers = ChannelService::getChannel()->servers;
                };break;
                case 'SERVER': {
                    $servers = [ServerService::getServer()];
                    if($servers[0]->tag == 'ALL') {
                        return ['code' => 1, 'msg' => trans('admin.server.invalid')];
                    }
                };break;
                default: {
                    return ['code' => 1, 'msg' => trans('admin.server.invalid'), 'server' => $server];
                }
            }

            $command = 'notice';
            $data = [
                'title' => $title,
                'content' => $content,
                'item' => $item
            ];

            $result = MachineService::send($server, $command, $data);

            if($result['error'] || $result['failed']) {
                return ['code' => 1, 'msg' => trans('admin.status.failure'), 'failed' => $result['failed'], 'error' => $result['error']];
            }

            $attributes = [
                'user_id' => AuthService::user()->id,
                'server' => json_encode($servers),
                'title' => $title,
                'content' => $content,
                'item' => json_encode($item)
            ];
    
            NoticeModel::create($attributes);

        } catch (Exception $e) {
            return ['code' => $e->getCode(), 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request)
    {
        return ['code' => 0, 'msg' => ''];
    }
}
