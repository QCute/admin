<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model {
    protected $table = '';
    public function __construct($name = '')
    {
        $this->table = $name;
        parent::__construct([]);
    }
}
