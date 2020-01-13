<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteTheme extends Model
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
     * @var bool
     */
    protected $fillable = [
        'is_default',
        'name', 
    ];

    /**
     * Scope a query to only the default item.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true)->take(1);
    }
}
