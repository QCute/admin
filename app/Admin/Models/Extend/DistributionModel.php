<?php

namespace App\Admin\Models\Extend;

use App\Admin\Database\DistributionConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;

abstract class DistributionModel extends Model
{
    /**
     * The rules.
     *
     * @var array<string, callable>
     * @example "number" => fn ($acc, $item) => $acc + $item,
     */
    private $rules = [];

    /**
     * Get the database connection for the model.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return new DistributionConnection($this->rules);
    }
}
