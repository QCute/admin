<?php

namespace App\Admin\Models\Admin;

use App\Admin\Models\Model;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionModel extends Model
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
    protected $table = 'role_permission';

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.role_permission_table');

        parent::__construct($attributes);
    }
}
