<?php

namespace App\Admin\Models\OperationModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class SensitiveWordModel extends Model {
    use DefaultDatetimeFormat;
    protected $table = 'sensitive_word_data';
}
