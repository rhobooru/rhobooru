<?php

namespace App\Services\Search;

use App\Models\Tag;

class SearchQuery
{
    /**
     * The base group within this query.
     *
     * @var SearchGroup
     */
    protected $root_group;

    /**
     * The original query string.
     *
     * @var string
     */
    protected $raw_query;

    /**
     * Yeah, it's fucky. Just put it into regex101.com if you
     * need to dick with it.
     *
     * Named Groups:
     *   starts_group: Will be present if this tag is
     *     preceeded by one or more '(' characters.
     *
     *   ends_group: Will be present if this tag is
     *     followed by one or more ')' characters.
     *
     *   query_term: Contains the entire individual term,
     *     minus any group constructs.
     *
     *   is_negative: Will be present if the tag is a
     *     negative term.
     *
     *   tag_name: Will contain the base tag, if it's
     *     not a fuzzy term.
     *
     *   fuzzy_tag_name: Will contain the base tag, if it
     *     is a fuzzy term.
     *
     *   ored_with_next: Will be present if this term is ORed
     *     with the following term.
     *
     * For the groups like is_negative, ored_with_next,
     * and starts_group, you shouldn't actually care what is
     * inside the group; only that the group is present or not.
     */
    protected $pattern = '/(?<starts_group>\(+)?(?<query_term>(?<is_negative>\-)?\[((?<tag_name>[^\[\*]+)|(?<fuzzy_tag_name>[^\[]+))\])(?<ored_with_next>\|)?(?<ends_group>\)+)?/i';

    public function __construct(string $query)
    {
        $this->raw_query = $query;

        $this->parseQuery();
    }

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
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

        $matches = $this->patternMatchQueryString();

        [$this->root_group] = $this->constructGroup(
            $matches,
            0,
            new SearchGroup
        );

        $this->resolveTags();
    }

    /**
     * Runs a regex against the raw string query to pull out each term.
     *
     * @return array The pattern matches.
     */
    private function patternMatchQueryString(): array
    {
        preg_match_all(
            $this->pattern,
            $this->raw_query,
            $matches,
            PREG_UNMATCHED_AS_NULL | PREG_SET_ORDER
        );

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
     * @param int $start
     * @param SearchGroup $group
     *
     * @return void
     */
    private function constructGroup(
        array $matches,
        int $start,
        SearchGroup $group
    ) {
        $count = count($matches);

        for ($i = $start; $i < $count; $i++) {
            $match = $matches[$i];

            if ($match['starts_group'] !== null) {
                $new_group_matches = $matches;
                $new_group_matches[$i]['starts_group'] = null;

                [$group->groups[], $i] = $this->constructGroup(
                    $new_group_matches,
                    $i,
                    new SearchGroup
                );

                continue;
            }

            $group->conjunction = $match['ored_with_next']
                ? SearchGroup::OR
                : SearchGroup::AND;

            $group->terms[] = SearchTerm::parseStringTerm($match);

            if ($match['ends_group'] !== null) {
                return [$group, $i + 1];
            }
        }

        return [$group, count($matches)];
    }
}
