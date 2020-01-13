<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentRating extends Model
{
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
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->orderBy('order', 'asc');
        });
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
