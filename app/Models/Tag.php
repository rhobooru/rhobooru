<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserAudits;

class Tag extends Model
{
    use SoftDeletes, UserAudits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'aliased_to_tag_id',
        'name', 
        'description',
    ];

    /**
     * Get the tag's records.
     */
    public function records()
    {
        return $this->belongsToMany('App\Models\Record');
    }

    /**
     * Get the tag's alias target
     */
    public function aliased_to()
    {
        return $this->belongsTo('App\Models\Tag', 'aliased_to_tag_id');
    }

    /**
     * Get the tag's aliases
     */
    public function aliases()
    {
        return $this->hasMany('App\Models\Tag', 'aliased_to_tag_id');
    }

    /**
     * Get the tag's associated tags when this is tag1.
     */
    public function tag1_associations()
    {
        return $this->hasMany('App\Models\TagAssociation', 'tag_id_1');
    }

    /**
     * Get the tag's associated tags when this is tag2.
     */
    public function tag2_associations()
    {
        return $this->hasMany('App\Models\TagAssociation', 'tag_id_2');
    }

    /**
     * Get the tag's associated tags.
     */
    public function tagAssociations()
    {
        return $this->tag1_associations->merge($this->tag2_associations);
    }

    /**
     * Removed default eager loads from query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoEagerLoads($query){
        return $query->setEagerLoads([]);
    }







    /**
     * Performs the side-effect actions of aliasing this tag to another tag, 
     * including moving records and correcting cached counts.
     * 
     * Called from TagObserver.updating(). This shouldn't need to be called manually!
     */
    public function doAliasSideEffects()
    {
        $tag_being_aliased = $this;

        // If our tag hasn't even had its alias updated, do nothing.
        if(!$tag_being_aliased->isDirty('aliased_to_tag_id'))
            return;

        $didHaveAlias = $tag_being_aliased->getOriginal('aliased_to_tag_id') != null;
        $willHaveAlias = $tag_being_aliased->aliased_to_tag_id != null;

        // If we're aliasing this tag...
        if(!$didHaveAlias && $willHaveAlias)
        {
            $this->aliased_to->records()->attach($this->records()->pluck('id'));

            // Delete any shared tag_record rows belonging to this tag.
            $this->records()->detach();
        }
        // We're re-aliasing the tag...
        else if ($didHaveAlias && $willHaveAlias)
        {
            // Do nothing. This tag had no items associated with it before
            // so there's nothing to move or detach.
        }
        // We're un-aliasing the tag...
        else if($didHaveAlias && !$willHaveAlias)
        {
            // Do nothing. We can't know which items are supposed to belong
            // to this tag now so any further work will need to be 
            // done manually.
        }
    }
}
