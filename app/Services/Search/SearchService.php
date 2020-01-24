<?php

namespace App\Services\Search;

use App\Models\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use SearchQuery;

class SearchService
{
    public static function searchRecords(string $raw_query, int $page, int $per_page): Collection
    {
        $tag_ids = self::getTagIds($raw_query);

        $record_ids = self::getRecordIds($tag_ids, $page, $per_page);

        return Record::whereIn('id', $record_ids)->get();
    }

    private static function getTagIds(string $raw_query): array
    {
        $parsed_query = new SearchQuery($raw_query);

        $tag_ids = [];

        foreach ($parsed_query->root_group->terms as $term) {
            $tag_ids = array_merge($tag_ids, $term->tag_ids);
        }

        return $tag_ids;
    }

    private static function getRecordIds(
        array $tag_ids,
        in $page,
        int $per_page
    ) {
        $search_query = DB::table('record_tag')
            ->select('record_id')
            ->whereIn('tag_id', $tag_ids)
            ->groupBy('record_id')
            ->havingRaw('count(tag_id) >= ' . count($tag_ids));

        // Pagination
        $search_query
            ->skip(($page - 1) * $per_page)
            ->take($per_page);

        return $search_query->pluck('record_id');
    }
}
