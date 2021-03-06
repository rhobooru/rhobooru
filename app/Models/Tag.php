<?php

namespace App\Models;

use App\Models\Traits\UserAudits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'summary',
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
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @codeCoverageIgnore
     */
    public function scopeNoEagerLoads($query)
    {
        return $query->setEagerLoads([]);
    }

    /**
     * Performs the side-effect actions of aliasing this tag to another tag,
     * including moving records and correcting cached counts.
     *
     * Called from TagObserver.updating(). This shouldn't need to be
     * called manually!
     *
     * @return null
     */
    public function doAliasSideEffects()
    {
        // If our tag hasn't even had its alias updated, do nothing.
        if (! $this->isDirty('aliased_to_tag_id')) {
            return;
        }

        $didHaveAlias = $this->getOriginal('aliased_to_tag_id') !== null;
        $willHaveAlias = $this->aliased_to_tag_id !== null;

        // If we're aliasing this tag...
        if (! $didHaveAlias && $willHaveAlias) {
            // Add any records from this tag to the alias tag.
            $this->aliased_to->records()
                ->attach($this->records()->pluck('id'));

            // Remove any records belonging to this tag.
            $this->records()->detach();
        }
    }
}
