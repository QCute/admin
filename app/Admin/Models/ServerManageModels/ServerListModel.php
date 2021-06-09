<?php

namespace App\Admin\Models\ServerManageModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class ServerListModel extends Model {
    use DefaultDatetimeFormat;
    protected $table = 'server_list';
}
