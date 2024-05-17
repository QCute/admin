<?php

namespace App\Admin\Models\GameData;

use App\Admin\Models\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClientErrorLogModel extends Model 
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
    protected $table = 'client_error_log';

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())
            ->withInput($input)
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
