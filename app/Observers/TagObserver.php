<?php

namespace App\Observers;

use App\Models\Tag;

class TagObserver
{
    public function __construct()
    { }

    /**
     * Handle the tag "created" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function created(Tag $tag)
    {
        //
    }

    /**
     * Handle the tag "updating" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function updating(Tag $tag)
    {
        if($tag->isDirty('aliased_to_tag_id'))
        {
            $tag->doAliasSideEffects();
        }
    }

    /**
     * Handle the tag "updated" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function updated(Tag $tag)
    {
        //
    }

    /**
     * Handle the tag "restored" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function restored(Tag $tag)
    {
        //
    }

    /**
     * Handle the tag "deleting" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function deleting(Tag $tag)
    {
        if($tag->isForceDeleting()) 
        {
            $this->forceDeleting($tag);
        }
    }

    /**
     * Handle the tag "force deleting" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function forceDeleting(Tag $tag)
    {
        //
    }

    /**
     * Handle the tag "deleted" event.
     *
     * @param  \App\Models\Tag  $tag
     * @return void
     */
    public function deleted(Tag $tag)
    {
        //
    }
}
