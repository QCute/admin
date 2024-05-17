<?php

namespace App\Admin\Models\Extend;

use App\Admin\Models\Model;

class RoleChannelModel extends Model
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
    protected $table = 'role_channel';

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.role_channel_table');

        parent::__construct($attributes);
    }
}
