<?php

namespace App\Admin\Models\GameDataModels;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;

class TableDataModel extends Model {
    use DefaultDatetimeFormat;
    protected $connection = '';
    protected $table = '';
    protected $primaryKey = '';
    public function __construct($connection = '', $table = '', $primaryKey = '')
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        parent::__construct();
    }
}
