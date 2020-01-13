<?php

namespace App\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RecordTag extends Pivot
{
    /**
     * Save the pivot model record to the database.
     *
     * @return int
     */
    public function save(array $options = Array())
    {
        event('eloquent.saving: ' . __CLASS__, $this);

        parent::save($options);

        event('eloquent.saved: ' . __CLASS__, $this);
    }

    /**
     * Delete the pivot model record from the database.
     *
     * @return int
     */
    public function delete()
    {
        event('eloquent.deleting: ' . __CLASS__, $this);

        parent::delete();

        event('eloquent.deleted: ' . __CLASS__, $this);
    }
}