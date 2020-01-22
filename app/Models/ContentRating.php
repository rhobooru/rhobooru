<?php

namespace App\Models;

use App\Scopes\SortedScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ContentRating extends Model implements Sortable
{
    use SortableTrait;

    /**
     * Whether to allow created_at and updated_at.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'available_to_anonymous',
        'order',
        'name', 
        'short_name',
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
     * Get the records that belong to this content rating.
     */
    public function records(){
        return $this->hasMany('App\Models\Record');
    }

    /**
     * Scope a query to the item with the maximum 'order'.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMaximum($query)
    {
        return $query->orderBy('order','desc')->take(1);
    }

    /**
     * Scope a query to the item with the minimum 'order'.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMinimum($query)
    {
        return $query->orderBy('order','asc')->take(1);
    }

    /**
     * Scope a query to the items available to anonymous users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('available_to_anonymous', true);
    }
}
