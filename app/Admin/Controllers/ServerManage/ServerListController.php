<?php

namespace App\Admin\Controllers\ServerManage;

use App\Admin\Builders\Form;
use App\Admin\Builders\Table;
use App\Admin\Builders\Table\Header;
use App\Admin\Builders\Table\Page;
use App\Admin\Controllers\Controller;
use App\Admin\Models\Extend\ChannelModel;
use App\Admin\Models\Extend\ChannelServerModel;
use App\Admin\Models\Extend\ServerModel;
use App\Admin\Models\Model;
use App\Admin\Services\Auth\AuthService;
use App\Admin\Services\Extend\ServerService;
use App\Admin\Traits\FormOutput;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServerListController extends Controller
{
    use FormOutput;

    public function index(Request $request)
    {
        $form = new Form();
        $form->hide();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.server_table'));

        $columns = collect($columns)->map(function($item) { return (object)$item; });

        $this->buildIndex($form, $columns);

        $header = $columns
            ->filter(function($item) {
                return $item->name != Model::DELETED_AT;
            })
            ->map(function($item) {
                return (new Header())->field($item->name)->title($item->comment ?? $item->name)->align()->minWidth(160);
            });

        $header->push((new Header())->field('')->title(trans('admin.table.operate'))->align()->width(220)->toolbar());

        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        $input = $request->except(['_token', 'page', 'perPage']);
        $paginator = ServerModel::getPage($page, $perPage, $input);
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

        $columns = Schema::connection('admin')->getColumns(config('admin.database.server_table'));

        $user = AuthService::user();
        $channels = ChannelModel::getChannels($user);

        $select = $form->select('channels')->label(trans('admin.channel'))->required();
        foreach($channels as $channel) {
            $select->option()->label($channel->name)->value($channel->id);
        }

        $servers = ServerService::getServers();

        foreach($columns as $column) {
            switch($column['name']) {
                case 'center': {
                    // center
                    $select = $form->select('center')->label(trans('admin.server.center'));
                    foreach($servers as $server) {
                        if($server->type === '') continue;
                        if($server->type == 'local') continue;
                        if($server->type == 'world') continue;
                        $select->option()->label($server->name)->value($server->id);
                    }
                };break;
                case 'world': {
                    // world
                    $select = $form->select('world')->label(trans('admin.server.world'));
                    foreach($servers as $server) {
                        if($server->type === '') continue;
                        if($server->type == 'local') continue;
                        if($server->type == 'center') continue;
                        $select->option()->label($server->name)->value($server->id);
                    }
                };break;
                case 'recommend': {
                    $select = $form->select('recommend')->label(trans('admin.server.recommend'));
                    $select->option()->value('new')->label(trans('admin.server.recommend.new'));
                    $select->option()->value('hot')->label(trans('admin.server.recommend.hot'));
                    $select->option()->value('recommend')->label(trans('admin.server.recommend'));            
                };break;
                default: {
                    $form = $this->buildCreate($form, [$column]);
                }
            }
        }

        return $form->name(trans('admin.form.create'))->build();
    }

    public function store(Request $request)
    {
        $attributes = $request->except(['_token', 'channels']);

        $channels = $request->input('channels');
        $channels = ChannelModel::findMany($channels);

        DB::beginTransaction();
        try {
            $server = ServerModel::create($attributes);

            $channelServers = $channels
                ->map(function($channel) use ($server) {
                    return ['channel_id' => $channel->id, 'server_id' => $server->id, Model::DELETED_AT => 0];
                })
                ->toArray();

            ChannelServerModel::upsert($channelServers, ['channel_id', 'server_id']);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }

        return ['code' => 0, 'msg' => ''];
    }

    public function show(Request $request, int $id)
    {
        $data = ServerModel::withOnly([])->find($id);

        $form = new Form();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.server_table'));

        $user = AuthService::user();
        $channels = ChannelModel::getChannels($user);

        $select = $form->select('channels')->label(trans('admin.channel'))->disabled();
        foreach($channels as $channel) {
            $select->option()->label($channel->name)->value($channel->id)->select($data->channels->contains($channel->id));
        }
        
        $form = $this->buildShow($form, $columns, $data);

        return $form->name(trans('admin.form.show'))->build();
    }

    public function edit(Request $request, int $id)
    {
        $data = ServerModel::withOnly([])->find($id);

        $form = new Form();

        $columns = Schema::connection('admin')->getColumns(config('admin.database.server_table'));

        $user = AuthService::user();
        $channels = ChannelModel::getChannels($user);

        $select = $form->select('channels')->label(trans('admin.channel'))->required();
        foreach($channels as $channel) {
            $isSelected = $data->channels->contains($channel->id);
            $select->option()->label($channel->name)->value($channel->id)->select($isSelected);
        }

        $form = $this->buildEdit($form, $columns, $data);

        return $form->name(trans('admin.form.edit'))->method('PATCH')->build();
    }

    public function update(Request $request, int $id)
    {
        $server = ServerModel::find($id);

        $attributes = $request->except(['_token', 'channels']);

        $channels = $request->input('channels');
        $channels = ChannelModel::findMany($channels);

        DB::beginTransaction();
        try {

            $server->update($attributes);

            $update = [];
            foreach($channels as $channel) {
                if(!$server->channels->contains($channel->id)) {
                    $update[] = $channel;
                }
            }

            $update = collect($update)
                ->map(function($channel) use ($server) {
                    return ['channel_id' => $channel->id, 'server_id' => $server->id, Model::DELETED_AT => 0];
                })
                ->toArray();

            ChannelServerModel::upsert($update, ['channel_id', 'server_id']);

            $destroy = [];
            foreach($server->channels as $channelServer) {
                if(!$channels->contains($channelServer->id)) {
                    $destroy[] = $channelServer;
                }
            }

            $destroy = collect($destroy)->map(function($channelServer) {
                return $channelServer->id;
            });

            ChannelServerModel::destroy($destroy);

            DB::commit();
        } catch(Exception $exception) {
            DB::rollBack();

            return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    
        return ['code' => 0, 'msg' => ''];
    }

    public function destroy(Request $request, int $id)
    {
        ServerModel::destroy($id);

        return ['code' => 0, 'msg' => ''];
    }
}
