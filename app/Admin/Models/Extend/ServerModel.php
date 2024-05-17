<?php

namespace App\Admin\Models\Extend;

use App\Admin\Models\Admin\RoleModel;
use App\Admin\Models\Admin\UserModel;
use App\Admin\Models\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServerModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'admin';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'server';

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.server_table');

        parent::__construct($attributes);
    }

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())->withInput($input)->paginate($perPage, ['*'], 'page', $page);
    }

    public static function getServer(int $channelId, int $serverId): ServerModel|null
    {
        return (new static())
            ->where('id', '=', $channelId)
            ->withWhereHas('channels', function (Builder $query) use ($channelId, $serverId) {
                return $query
                    ->where('channel_id', '=', $channelId)
                    ->where('server_id', '=', $serverId);
            })
            ->first();
    }

    public static function getServers(UserModel $user)
    {
        $roles = collect($user->roles)->map(function($role) { return $role->id; });

        return (new static())
            ->withWhereHas('roles', function (Builder $query) use ($roles) {
                return $query->whereIn('role.id', $roles);
            })
            ->withWhereHas('channels', function (Builder $query) use ($roles) {
                return $query
                    ->withWhereHas('roles', function(Builder $query) use ($roles) {
                        return $query->whereIn('role.id', $roles);
                    })
                    ->orWhere('permission', '=', '');
            })
            ->orWhere('permission', '=', '')
            ->get()
            ->keyBy('id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(RoleModel::class, config('admin.database.role_server_table'),'server_id', 'role_id');
    }

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(ChannelModel::class, config('admin.database.channel_server_table'), 'server_id', 'channel_id');
    }

    public function getChannelAttribute() : ChannelModel|null
    {
        return $this->channels[0] ?? null;
    }
}
