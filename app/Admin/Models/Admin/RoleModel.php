<?php

namespace App\Admin\Models\Admin;

use App\Admin\Models\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoleModel extends Model
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
    protected $table = 'role';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['permissions'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.role_table');

        parent::__construct($attributes);
    }

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())->withInput($input)->paginate($perPage, ['*'], 'page', $page);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(PermissionModel::class, config('admin.database.role_permission_table'), 'role_id', 'permission_id');
    }
}
