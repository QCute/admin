<?php

namespace App\Admin\Models\Admin;

use App\Admin\Models\Model;
use Illuminate\Database\Eloquent\Collection;

class UserRoleModel extends Model
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
    protected $table = 'user_role';

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.user_role_table');

        parent::__construct($attributes);
    }
}
