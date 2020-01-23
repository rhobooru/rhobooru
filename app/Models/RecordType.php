<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordType extends Model
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
        'requires_player_controls',
        'name',
        'description',
    ];

    /**
     * Get the records that belong to this record type.
     */
    public function records()
    {
        return $this->hasMany('App\Models\Record');
    }

    /**
     * Get the media formats that belong to this record type.
     */
    public function media_formats()
    {
        return $this->hasMany('App\Models\MediaFormat');
    }

    /**
     * Scope a query to the items that require media controls.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresMediaControls($query)
    {
        return $query->where('requires_player_controls', true);
    }
}
