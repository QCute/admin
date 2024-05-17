<?php

namespace App\Admin\Services\Extend;

use App\Admin\Models\Extend\ChannelModel;
use App\Admin\Services\Auth\AuthService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;

class ChannelService
{
    /**
     * Nav bar channel list
     * @param int $role_id role id
     *
     * @return Collection
     */
    public static function getChannels(int|null $role_id = null): Collection
    {
        $user = AuthService::user();

        $channels = ChannelModel::getChannels($user);

        return $channels;
    }

    /**
     * Get channel
     * @param int|null $id
     * 
     * @return ChannelModel|null
     */
    public static function getChannel(int|null $id = null): ChannelModel|null
    {
        $channels = self::getChannels();

        $id = $id ?? Cookie::get('channel') ?? $channels->first()->id;

        return $channels->find($id);
    }

    /**
     * Save server
     * @param int|null $id
     * 
     */
    public static function saveChannel(int $id)
    {
        Cookie::queue('channel', $id);
    }

    /**
     * Has channel
     *
     * @return bool
     */
    public static function hasChannel(int $id): bool 
    {
        return self::getChannels()->has($id);
    }
}
