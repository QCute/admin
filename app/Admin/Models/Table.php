<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model {
    public function __construct($name = '')
    {
        $this->table = $name;
        parent::__construct([]);
    }
}
