<?php

namespace App\Admin\Models\OperationModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class MaintainNoticeModel extends Model {
    use DefaultDatetimeFormat;
    protected $table = 'maintain_notice';
}
