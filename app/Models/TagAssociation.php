<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagAssociation extends Model
{
    /**
     * Whether to allow created_at and updated_at.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ['tag_id_1', 'tag_id_2'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_id_1',
        'tag_id_2',
        'tag_association_type_id',
    ];

    /**
     * Get the first tag.
     */
    public function tag1()
    {
        return $this->belongsTo('App\Models\Tag', 'tag_id_1');
    }

    /**
     * Get the second tag.
     */
    public function tag2()
    {
        return $this->belongsTo('App\Models\Tag', 'tag_id_2');
    }

    /**
     * Get the tag association type.
     */
    public function tag_association_type()
    {
        return $this->belongsTo('App\Models\TagAssociationType');
    }

    /**
     * Scope a query to the requested TagAssociationType.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int                                    $tag_association_type_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssociationType($query, $tag_association_type_id)
    {
        return $query->where('tag_association_type_id', $tag_association_type_id);
    }
}
