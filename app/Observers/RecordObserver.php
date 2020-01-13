<?php

namespace App\Observers;

use App\Models\Record;

class RecordObserver
{
    public function __construct()
    { }

    /**
     * Handle the record "created" event.
     *
     * @param  \App\Models\Record  $record
     * @return void
     */
    public function created(Record $record)
    {
        //
    }

    /**
     * Handle the record "updated" event.
     *
     * @param  \App\Models\Record  $record
     * @return void
     */
    public function updated(Record $record)
    {
        //
    }

    /**
     * Handle the record "deleted" event.
     *
     * @param  \App\Models\Record  $record
     * @return void
     */
    public function deleted(Record $record)
    {
        //
    }

    /**
     * Handle the record "restored" event.
     *
     * @param  \App\Models\Record  $record
     * @return void
     */
    public function restored(Record $record)
    {
        //
    }

    /**
     * Handle the record "force deleting" event.
     *
     * @param  \App\Models\Record  $record
     * @return void
     */
    public function forceDeleting(Record $record)
    {
        $record->decrementRelationshipCounts();
    }

    /**
     * Handle the record "deleting" event.
     *
     * @param  \App\Models\Record  $record
     * @return void
     */
    public function deleting(Record $record)
    {
        if($record->isForceDeleting()) 
        {
            $this->forceDeleting($record);
        }
    }
}
