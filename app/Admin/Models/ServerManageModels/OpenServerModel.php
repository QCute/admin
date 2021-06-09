<?php

namespace App\Admin\Models\ServerManageModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class OpenServerModel extends Model {
    use DefaultDatetimeFormat;
    protected $table = '';
}
