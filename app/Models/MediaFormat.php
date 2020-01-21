<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFormat extends Model
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
        'extension',
        'mime',
        'record_type_id',
        'can_produce_thumbnails',
        'accepted_for_upload',
    ];

    /**
     * Get the record type for this media format.
     */
    public function record_type(){
        return $this->belongsTo('App\Models\RecordType');
    }

    /**
     * Scope a query to the items that require media controls.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresMediaControls($query)
    {
        return $query->whereHas('record_type', function (\Illuminate\Database\Eloquent\Builder $q) {
            $q->requiresMediaControls();
        });
    }

    /**
     * Scope a query to the items that rcan be uploaded.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAcceptedForUpload($query)
    {
        return $query->where('accepted_for_upload', true);
    }
}
