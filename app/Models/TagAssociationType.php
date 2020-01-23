<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagAssociationType extends Model
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
        'name',
        'description',
    ];

    /**
     * Get the tag-tag relationships using this type.
     */
    public function tag_associations()
    {
        return $this->hasMany('App\Models\TagAssociation');
    }
}
