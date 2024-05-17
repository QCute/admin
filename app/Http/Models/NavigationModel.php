<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Collection;

class NavigationModel extends Model
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
    protected $table = 'navigation';

    public function __construct()
    {
        $this->table = config('web.database.navigation_table');
    }

    public static function getNavigationList(): Collection
    {
        return (new static())->with('children')->where('parent_id', 0)->orderBy('order')->get();
    }

    public function children()
    {
        return $this->hasMany(NavigationModel::class, 'parent_id', 'id');
    }
}
