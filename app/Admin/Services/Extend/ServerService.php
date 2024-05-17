<?php

namespace App\Admin\Services\Extend;

use App\Admin\Models\Extend\ServerModel;
use App\Admin\Services\Auth\AuthService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;

class ServerService
{
    /**
     * Nav bar server list
     * @param int $channel_id channel id
     *
     * @return Collection<int, ServerModel>
     */
    public static function getServers(int|null $channel_id = null): Collection
    {
        if(is_null($channel_id)) {
            $user = AuthService::user();
            $servers = ServerModel::getServers($user);
        } else {
            $servers = ChannelService::getChannel($channel_id)->servers;
        }

        return $servers;
    }

    /**
     * Get server
     * @param int|null $id
     * 
     * @return ServerModel|null
     */
    public static function getServer(int|null $id = null): ServerModel|null
    {
        $servers = self::getServers();

        $id = $id ?? Cookie::get('server') ?? $servers->first()->id;

        return $servers->find($id);
    }

    /**
     * Save server
     * @param int|null $id
     * 
     */
    public static function saveServer(int $id)
    {
        Cookie::queue('server', $id);
    }

    /**
     * Has server
     *
     * @return bool
     */
    public static function hasServer(int $id): bool 
    {
        return self::getServers()->has($id);
    }
}
