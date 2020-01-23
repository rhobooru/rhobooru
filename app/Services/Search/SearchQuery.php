<?php

namespace App\Services\Search;

use App\Models\Tag;
use SearchGroup;
use SearchTerm;

class SearchQuery
{
    /**
     * The base group within this query.
     *
     * @var SearchGroup
     */
    public $root_group;

    /**
     * The original query string.
     *
     * @var string
     */
    public $raw_query;

    public function __construct(string $query)
    {
        $this->raw_query = $query;

        $this->parseQuery();
    }

    /**
     * Returns a flat array of all terms in this query, recursively.
     *
     * @return array
     */
    public function terms(): array
    {
        return $this->root_group->allTerms();
    }

    /**
     * Processes a string query into a SearchGroup.
     *
     * @return void
     */
    private function parseQuery()
    {
        if ($this->raw_query === null) {
            return;
        }

        $matches = $this->patternMatchQueryString($this->raw_query);

        [$this->root_group, $trash] = $this->constructGroup($matches, 0, new SearchGroup());

        $this->resolveTags();
    }

    /**
     * Runs a regex against the raw string query to pull out each term.
     *
     * @param string $query
     *
     * @return array The pattern matches.
     */
    private function patternMatchQueryString(string $query): array
    {
        // Yeah, it's fucky. Just put it into regex101.com if you need to dick with it.
        //
        // Named Groups:
        //      starts_group: Will be present if this tag is preceeded by one or more '(' characters.
        //      ends_group: Will be present if this tag is followed by one or more ')' characters.
        //      query_term: Contains the entire individual term, minus any group constructs.
        //      is_negative: Will be present if the tag is a negative term.
        //      tag_name: Will contain the base tag, if it's not a fuzzy term.
        //      fuzzy_tag_name: Will contain the base tag, if it is a fuzzy term.
        //      ored_with_next: Will be present if this term is ORed with the following term.
        //
        // For the groups like is_negative, ored_with_next, and starts_group, you shouldn't
        // actually care what is inside the group; only that the group is present or not.
        $pattern = '/(?<starts_group>\(+)?(?<query_term>(?<is_negative>\-)?\[((?<tag_name>[^\[\*]+)|(?<fuzzy_tag_name>[^\[]+))\])(?<ored_with_next>\|)?(?<ends_group>\)+)?/i';

        preg_match_all($pattern, $query, $matches, PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER);

        return $matches;
    }

    /**
     * Assigns tag ids to the SearchTerms.
     *
     * @return void
     */
    private function resolveTags()
    {
        $terms = $this->root_group->allTerms();
        $tag_names = array_map(static function($item) {
            return $item->phrase;
        }, $terms);

        $tag_ids = Tag::whereIn('name', $tag_names)
            ->select(['tags.id', 'name'])
            ->get();

        $this->root_group->assignTagIds($tag_ids);
    }

    /**
     * Builds a SearchGroup out of a string query's pattern matches.
     *
     * @param array $matches
     * @param int $current_index
     * @param SearchGroup $current_group
     *
     * @return void
     */
    private function constructGroup(array $matches, int $current_index, SearchGroup $current_group)
    {
        $count = count($matches);

        for ($i = $current_index; $i < $count; $i++) {
            $match = $matches[$i];

            if (array_key_exists('starts_group', $match) && $match['starts_group'] !== null) {
                $new_group_matches = $matches;
                $new_group_matches[$i]['starts_group'] = null;

                [$current_group->groups[], $i] = $this->constructGroup($new_group_matches, $i, new SearchGroup());

                continue;
            }

            if (array_key_exists('ored_with_next', $match) && $match['ored_with_next'] !== null) {
                $current_group->conjunction = SearchGroup::MODE_OR;
            }

            $current_group->terms[] = SearchTerm::parseStringTerm($match);

            if (array_key_exists('ends_group', $match) && $match['ends_group'] !== null) {
                $i++;

                return [$current_group, $i - 1];
            }
        }

        return [$current_group, count($matches)];
    }
}
