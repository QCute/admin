<?php

namespace App\Api\Models;
use Illuminate\Database\Eloquent\Collection;

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

    public static function getNotices(string $channel): Collection
    {
        return (new static())->where('channel', $channel)->select(['title', 'content', 'start_time', 'end_time'])->get();
    }
}
