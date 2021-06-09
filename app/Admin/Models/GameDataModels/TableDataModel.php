<?php

namespace App\Admin\Models\GameDataModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class TableDataModel extends Model {
    use DefaultDatetimeFormat;
    protected $connection = '';
    protected $table = '';
    public function __construct($connection = '', $table = '')
    {
        $this->connection = $connection;
        $this->table = $table;
        parent::__construct();
    }
}
