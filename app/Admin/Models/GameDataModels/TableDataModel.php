<?php

namespace App\Admin\Models\GameDataModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class TableDataModel extends Model {
    use DefaultDatetimeFormat;
    protected $table = '';
    public function __construct($name = '')
    {
        $this->table = $name;
        parent::__construct();
    }
}
