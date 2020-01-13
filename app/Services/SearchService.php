<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\SearchQuery;

class SearchService
{
    public static function searchRecords(string $raw_query, int $page, int $results_per_page): Collection
    {
        $parsed_query = new SearchQuery($raw_query);

        $tag_ids = [];

        foreach($parsed_query->root_group->terms as $term)
        {
            $tag_ids = array_merge($tag_ids, $term->tag_ids);
        }

        $search_query = \DB::table('record_tag')
            ->select('record_id')
            ->whereIn('tag_id', $tag_ids)
            ->groupBy('record_id')
            ->havingRaw('count(tag_id) >= ' . count($tag_ids));

        // Pagination
        $search_query
            ->skip(($page - 1) * $results_per_page)
            ->take($results_per_page);

        $record_ids = $search_query->pluck('record_id');

        $records = \App\Models\Record::
            whereIn('id', $record_ids)
            ->get();

        return $records;
    }
}