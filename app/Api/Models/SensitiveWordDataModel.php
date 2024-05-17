<?php

namespace App\Api\Models;

class SensitiveWordDataModel extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'api';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sensitive_word_data';
}
