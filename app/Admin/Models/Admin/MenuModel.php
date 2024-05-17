<?php

namespace App\Admin\Models\Admin;

use App\Admin\Models\Model;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MenuModel extends Model
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
    protected $table = 'menu';

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.menu_table');

        parent::__construct($attributes);
    }

    public static function findMenu(UserModel $user, string $path): MenuModel|null
    {
        $paths = $user
            ->roles
            ->map(function($role) { 
                return $role->permissions;
            })
            ->flatten()
            ->filter(function($permission) {
                return $permission->http_method == '' || $permission->http_method == 'GET';
            })
            ->map(function($permission) {
                return explode("\r\n", $permission->http_path);
            })
            ->flatten()
            ->map(function($item) {
                return str_replace('*', '%', $item);
            });

        return (new static())
            ->with('children')
            ->where('url', '=', $path)
            ->where(function(Builder $query) use ($paths) {
                foreach ($paths as $path) {
                    $query->orWhere('url', 'LIKE', $path);
                }
            })
            ->orderBy('order')
            ->first();

    }

    public static function getMenus(UserModel $user): Collection
    {
        $paths = $user
            ->roles
            ->map(function($role) { 
                return $role->permissions;
            })
            ->flatten()
            ->filter(function($permission) {
                return $permission->http_method == '' || str_contains($permission->http_method, 'GET');
            })
            ->map(function($permission) {
                return explode("\r\n", $permission->http_path);
            })
            ->flatten()
            ->map(function($item) {
                return str_replace('*', '%', $item);
            });

        return (new static())
            ->with('children')
            ->where('parent_id', '=', 0)
            ->where(function(Builder $query) use ($paths) {
                foreach ($paths as $path) {
                    $query->orWhere('url', 'LIKE', $path);
                }
            })
            ->orderBy('order')
            ->get()
            ->keyBy('id');
    }

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())->withInput($input)->paginate($perPage, ['*'], 'page', $page);
    }

    public function children()
    {
        return $this->hasMany(MenuModel::class, 'parent_id', 'id');
    }
}
