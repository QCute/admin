<?php

namespace App\Admin\Models\OperationModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class ImpeachModel extends Model {
    use DefaultDatetimeFormat;
    protected $table = 'impeach';
}
