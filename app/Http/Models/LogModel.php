<?php

namespace App\Http\Models;

class LogModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'web';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log';
}
