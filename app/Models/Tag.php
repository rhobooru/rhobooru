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
        return $this->belongsToMany('App\Models\Record')
            ->using(\App\Pivots\RecordTag::class);
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
     * Scope a query to only include tags with the given translations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string $translationField
     * @param  array $values
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereTranslationIn($query, string $translationField, array $values)
    {
        return $query->whereHas('translations', function ($query) use ($translationField, $values) {
            $query->whereIn($this->getTranslationsTable().'.'.$translationField, $values);
        });
    }

    /**
     * Joins this models translations to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJoinTranslations($query)
    {
        return $query->join('tag_translations', 'tags.id', '=', 'tag_translations.tag_id');
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
            // Decrement the tag count for shared records.
            // These records are losing this tag and not gaining the alias.
            /*
                SELECT
                    old.record_id
                FROM
                        record_tag old
                    INNER JOIN
                        record_tag new
                            ON new.tag_id = $alias_id
                            AND new.record_id = old.record_id
                WHERE
                    old.tag_id = $tag_id
            */
            $shared_record_ids = \DB::table('record_tag AS old')
                ->join('record_tag AS new', function ($join) use ($tag_being_aliased) {
                    $join->where('new.tag_id', '=', $tag_being_aliased->aliased_to_tag_id)
                    ->on('new.record_id', '=', 'old.record_id');
                })
                ->where('old.tag_id', '=', $tag_being_aliased->id)
                ->pluck('old.record_id')->toArray();

            \DB::table('records')->whereIn('id', $shared_record_ids)->decrement('cache_tag_count');



            // Add all my records to the alias, if they don't already exist on the alias.
            // The cache_tag_count for these records won't change since they're losing
            // one tag and gaining one tag (-1 + 1 = 0).
            /*
                UPDATE
                    old.tag_id = $alias_id
                FROM
                        record_tag old
                    LEFT JOIN
                        record_tag new
                            ON old.record_id = new.record_id
                            AND new.tag_id = $alias_id
                WHERE
                        old.tag_id = $tag_id
                    AND 
                        new.record_id IS NULL
            */
            $affected = \DB::table('record_tag')
                ->leftJoin('record_tag AS new', function ($join) use ($tag_being_aliased) {
                    $join->where('new.tag_id', '=', $tag_being_aliased->aliased_to_tag_id)
                    ->on('new.record_id', '=', 'record_tag.record_id');
                })
                ->where('record_tag.tag_id', '=', $tag_being_aliased->id)
                ->whereNull('new.record_id')
                ->update(['record_tag.tag_id' => $tag_being_aliased->aliased_to_tag_id]);
                


            // Add the count of records moved to my alias' cache_record_count field.
            \DB::table('tags')
                ->where('id', $tag_being_aliased->aliased_to_tag_id)
                ->increment('cache_record_count', $affected);



            // Set my cache_record_count field to '0'.
            $tag_being_aliased->cache_record_count = 0;

            

            // Delete any shared tag_record rows belonging to this tag.
            \DB::table('record_tag')
                ->where('tag_id', '=', $tag_being_aliased->id)
                ->delete();
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

    /**
     * Decrements all of this Tag's relationships' `cached_tag_count` fields.
     *
     * @return void
     */
    public function decrementRelationshipCounts()
    {
        $this->decrementRecordTagCount();
    }

    /**
     * Decrements `records`.`cache_tag_count` for records associated with this
     * tag. Used when a tag is being force deleted.
     */
    public function decrementRecordTagCount()
    {
        \DB::table('records')
            ->join('record_tag', 'record_tag.record_id', '=', 'records.id')
            ->where('record_tag.tag_id', $this->id)
            ->decrement('records.cache_tag_count');
    }

    /**
     * Recalculates `cache_record_count` column for the provided Tag.
     *
     * @return int Calculated relationship count.
     */
    public function recalculateRecordCount()
    {
        // Check if we're given a tag with an 
        // earger-loaded relationship count.
        if($this->records_count !== null)
        {
            $this->cache_record_count = $this->records_count;
        }
        else
        {
            // Manually count the relationships.
            $this->cache_record_count = $this->records()->count();
        }

        $this->save();

        return $this->cache_record_count;
    }

    /**
     * Recalculates `cache_record_count` column for all Tags.
     *
     * @param int     $start_id             PK of the Tag at which to start processing.
     * @param int     $number_of_records    Number of Tags to process in this round.
     * @return int PK of the last tag processed.
     */
    public static function recalculateAllRecordCounts(int $start_id, int $number_of_records)
    {
        // Grab the slice of tags we want to process
        // this round with eager-loaded record count.
        $tags_to_process = Tag::withTrashed()
            ->withCount('records')
            ->where('id', '>=', $start_id)
            ->take($number_of_records)
            ->get();

        if($tags_to_process->isEmpty())
        {
            return null;
        }

        foreach($tags_to_process as $tag)
        {
            $tag->recalculateRecordCount();
        }

        return $tags_to_process->last()->id;
    }
}
