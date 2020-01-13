<?php

namespace App\GraphQL\Mutations;

use \App\Models\Tag;
use \App\Models\Record;
use Spatie\Permission\Models\Role;

class SyncRecordTags
{
    /**
     * Sync tags for a record.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return \App\Models\Record
     */
    public function __invoke($root, array $args): Record
    {
        $record_id = $args['record_id'];
        $tag_ids = $args['tag_ids'];

        $record = Record::findOrFail($record_id);

        $record->tags()->sync($tag_ids);

        $record->refresh();

        return $record;
    }
}