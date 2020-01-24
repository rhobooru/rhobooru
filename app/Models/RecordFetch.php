<?php

namespace App\Models;

use App\Scopes\SortedScope;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class RecordFetch extends Eloquent implements Sortable
{
    use SortableTrait;

    /**
     * Whether to allow created_at and updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var bool
     */
    protected $table = 'record_fetches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_default',
        'name',
        'description',
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
     * Scope a query to only the default item.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->take(1);
    }
}
