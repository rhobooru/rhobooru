<?php

namespace App\Models;

use App\Scopes\SortedScope;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Setting extends Eloquent implements Sortable
{
    use SortableTrait;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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
    public function setting_group()
    {
        return $this->belongsTo('App\Models\SettingGroup');
    }

    /**
     * Scope for the sortable code.
     *
     * @return void
     */
    public function buildSortQuery()
    {
        // Retrict sort logic to just this item's siblings.
        return static::query()
            ->where('system_setting', $this->system_setting === 1 ? true : false)
            ->where('setting_group_id', $this->setting_group_id);
    }
}
