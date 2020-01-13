<?php

namespace App\GraphQL\Mutations;

use \App\Models\Tag;
use \App\Models\Record;
use Spatie\Permission\Models\Role;

class RemoveTagFromRecord
{
    /**
     * Removes a tag from a record.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return \App\Models\Record
     */
    public function __invoke($root, array $args): Record
    {
        $tag_id = $args['tag_id'];
        $record_id = $args['record_id'];

        $tag = Tag::findOrFail($tag_id);
        $record = Record::findOrFail($record_id);

        if(!$record->tags()->where('id', $tag_id)->exists())
        {
            throw new \Exception("Tag ($tag_id) does not exist for Record ($record_id).");
        }

        $record->tags()->detach($tag);

        $record->refresh();

        return $record;
    }
}