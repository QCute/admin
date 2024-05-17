<?php

namespace App\Admin\Models\Extend;

use App\Admin\Models\Model;

class ChannelServerModel extends Model
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
    protected $table = 'channel_server';

    public function __construct(array $attributes = [])
    {
        $this->table = config('admin.database.channel_server_table');

        parent::__construct($attributes);
    }
}
