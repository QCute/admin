<?php

namespace App\Admin\Models\Admin;

use App\Admin\Models\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserModel extends Model implements AuthenticatableContract
{
    use Authenticatable;
    
    protected $fillable = ['username', 'password', 'name', 'avatar'];

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
    protected $table = 'user';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['roles', 'roles.permissions'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.user_table');

        parent::__construct($attributes);
    }

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())->withInput($input)->paginate($perPage, ['*'], 'page', $page);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(RoleModel::class, config('admin.database.user_role_table'), 'user_id', 'role_id');
    }
}
