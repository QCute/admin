<?php

namespace App\Admin\Controllers\Extend;

use App\Admin\Controllers\Controller;
use App\Admin\Services\Extend\ChannelService;
use App\Admin\Services\Extend\ServerService;

class ChannelController extends Controller
{
    public function index()
    {
        return ChannelService::getChannels();
    }

    public function change()
    {
        $id = request()->input('channel');

        $channel = ChannelService::getChannel($id);

        if(is_null($channel)) {
            return [
                'code' => 403, 
                'msg' => trans('admin.channel_not_found')
            ];
        }

        ChannelService::saveChannel($id);

        $server = $channel->servers->first();

        ServerService::saveServer($server->id);

        return ['code' => 0, 'msg' => ''];
    }
}
