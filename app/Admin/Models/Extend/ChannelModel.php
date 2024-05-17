<?php

namespace App\Admin\Models\Extend;

use App\Admin\Models\Admin\RoleModel;
use App\Admin\Models\Admin\UserModel;
use App\Admin\Models\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChannelModel extends Model
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
    protected $table = 'channel';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['roles', 'servers'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.channel_table');

        parent::__construct($attributes);
    }

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())->withInput($input)->paginate($perPage, ['*'], 'page', $page);
    }

    public static function getChannel(UserModel $user, int $channelId): ServerModel|null
    {
        $roles = collect($user->roles)->map(function($role) { return $role->id; });

        return (new static())
            ->where('id', '=', $channelId)
            ->withWhereHas('roles', function (Builder $query) use ($roles) {
                return $query->whereIn('role.id', $roles);
            })
            ->first();
    }

    public static function getChannels(UserModel $user): Collection
    {
        $roles = collect($user->roles)->map(function($role) { return $role->id; });

        return (new static())
            ->withWhereHas('roles', function (Builder $query) use ($roles) {
                return $query->whereIn('role.id', $roles);
            })
            ->withWhereHas('servers', function (Builder $query) use ($roles) {
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
        return $this->belongsToMany(RoleModel::class, config('admin.database.role_channel_table'),'channel_id', 'role_id');
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(ServerModel::class, config('admin.database.channel_server_table'), 'channel_id', 'server_id');
    }
}
