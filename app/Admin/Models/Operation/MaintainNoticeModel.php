<?php

namespace App\Admin\Models\Operation;

use App\Admin\Models\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MaintainNoticeModel extends Model 
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
    protected $table = 'maintain_notice';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts  = [
        'start_time' => 'datetime:Y-m-d H:i:s',
        'end_time' => 'datetime:Y-m-d H:i:s',
    ];

    public static function getPage(int $page, int $perPage, array $input = []): LengthAwarePaginator
    {
        return (new static())
            ->withInput($input)
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
