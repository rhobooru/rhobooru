<?php

namespace App\Observers;

use App\Pivots\RecordTag;

class RecordTagObserver
{
    /**
     * Handle the record tag "created" event.
     *
     * @param  \App\Pivots\RecordTag  $recordTag
     * @return void
     */
    public function created(RecordTag $recordTag)
    {
        \DB::table('tags')->where('id', $recordTag->tag_id)->increment('cache_record_count');
        \DB::table('records')->where('id', $recordTag->record_id)->increment('cache_tag_count');
    }

    /**
     * Handle the record tag "deleting" event.
     *
     * @param  \App\Pivots\RecordTag  $recordTag
     * @return void
     */
    public function deleting(RecordTag $recordTag)
    {
        // Insert some flags into the pivot object
        // to track our decrements later.
        $recordTag->decremented = false;

        // Make sure this relationship even exists. Laravel seems to
        // enjoy firing events on in-memory models.
        if(!RecordTag::where('record_id', $recordTag->record_id)
            ->where('tag_id', $recordTag->tag_id)
            ->exists())
        {
            $recordTag->decremented = true;
        }
    }

    /**
     * Handle the record tag "deleted" event.
     *
     * @param  \App\Pivots\RecordTag  $recordTag
     * @return void
     */
    public function deleted(RecordTag $recordTag)
    {
        // Deleted gets called once for each side of the relationship
        // so we need to only decrement once for each related model.

        if(!$recordTag->decremented)
        {
            \DB::table('tags')->where('id', $recordTag->tag_id)->decrement('cache_record_count');
            \DB::table('records')->where('id', $recordTag->record_id)->decrement('cache_tag_count');

            $recordTag->decremented = true;
        }
    }
}
