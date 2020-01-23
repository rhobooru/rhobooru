<?php

namespace App\Models;

use App\Scopes\SortedScope;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class SettingGroup extends Eloquent implements Sortable
{
    use SortableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'setting_group_id',
        'sort',
    ];

    /**
     * Sortable confg.
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        // @codeCoverageIgnoreStart
        parent::boot();

        static::addGlobalScope(new SortedScope);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get the group to which this group belongs.
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\SettingGroup', 'setting_group_id');
    }

    /**
     * Get the groups which belong to this group.
     */
    public function children()
    {
        return $this->hasMany('App\Models\SettingGroup');
    }

    /**
     * Scope for the sortable code.
     *
     * @return void
     */
    public function buildSortQuery()
    {
        // Retrict sort logic to just this item's siblings.
        return static::query()->where('setting_group_id', $this->setting_group_id);
    }
}
