<?php

namespace App\Admin\Models\Assistant;

use App\Admin\Models\Model;

class SSHKeyModel extends Model
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
    protected $table = 'ssh_key';
}
