<?php

namespace App\Admin\Models\GameDataModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class ClientErrorLogModel extends Model {
    use DefaultDatetimeFormat;
    protected $table = 'client_error_log';
}
