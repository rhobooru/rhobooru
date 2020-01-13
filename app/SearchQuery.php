<?php

namespace App;

use \App\Models\Tag;
use Illuminate\Support\Collection;

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

    function __construct(string $query)
    {
        $this->raw_query = $query;

        $this->parseQuery();
    }

    /**
     * Returns a flat array of all terms in this query, recursively.
     *
     * @return array
     */
    public function terms() : array
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
        if($this->raw_query == null)
            return;

        $matches = $this->patternMatchQueryString($this->raw_query);
        
        [$this->root_group, $trash] = $this->constructGroup($matches, 0, new SearchGroup());

        $this->resolveTags();
    }

    /**
     * Runs a regex against the raw string query to pull out each term.
     *
     * @param string $query
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
        $tag_names = array_map(function($item){ return $item->phrase; }, $terms);

        $tag_ids = Tag::whereIn('name', $tag_names)
            ->select(['tags.id', 'name'])
            ->get();

        $this->root_group->assignTagIds($tag_ids);
    }

    /**
     * Builds a SearchGroup out of a string query's pattern matches.
     *
     * @param array $matches
     * @param integer $current_index
     * @param SearchGroup $current_group
     * @return void
     */
    private function constructGroup(array $matches, int $current_index, SearchGroup $current_group)
    {
        for($i = $current_index; $i < count($matches); $i++)
        {
            $match = $matches[$i];

            if(array_key_exists('starts_group', $match) && $match['starts_group'] != null)
            {
                $new_group_matches = $matches;
                $new_group_matches[$i]['starts_group'] = null;

                [$current_group->groups[], $i] = $this->constructGroup($new_group_matches, $i, new SearchGroup());

                continue;
            }
            else
            {
                if(array_key_exists('ored_with_next', $match) && $match['ored_with_next'] != null)
                {
                    $current_group->conjunction = SearchGroup::OR;
                }

                $current_group->terms[] = SearchTerm::parseStringTerm($match);

                if(array_key_exists('ends_group', $match) && $match['ends_group'] != null)
                {
                    return [$current_group, $i++];
                }
            }
        }

        return [$current_group, count($matches)];
    }
}

class SearchGroup
{
    public const AND = 'and';

    public const OR = 'or';

    /**
     * The SearchTerms of this groups.
     *
     * @var array
     */
    public $terms;

    /**
     * Nested SearchGroups within this group.
     *
     * @var array
     */
    public $groups;

    /**
     * Whether this is an AND or an OR group.
     *
     * @var string
     */
    public $conjunction = SearchGroup::AND;

    /**
     * Returns a flat array of all terms in this group, recursively.
     *
     * @return array
     */
    public function allTerms() : array
    {
        $terms = $this->terms ?? [];

        if($this->groups)
        {
            foreach($this->groups as $group)
            {
                $terms = array_merge($terms, $group->allTerms());
            }
        }

        return $terms;
    }

    /**
     * Assigns ids to the tags terms in the group and its child groups.
     *
     * @param Collection $tag_ids
     * @return void
     */
    public function assignTagIds(Collection $tag_ids)
    {
        if($this->terms)
        {
            foreach($this->terms as $term)
            {
                $term->tag_ids[] = $tag_ids->firstWhere('name', $term->phrase)->id;
            }
        }

        if($this->groups)
        {
            foreach($this->groups as $group)
            {
                $group->assignTagIds($tag_ids);
            }
        }
    }
}

class SearchTerm
{
    /**
     * Whether this term is a negative search term.
     *
     * @var bool
     */
    public $is_negative;

    /**
     * Whether this term's phrase should be expanded.
     *
     * @var bool
     */
    public $is_fuzzy;

    /**
     * Whether the base phrase is a tag.
     *
     * @var bool
     */
    public $is_tag;

    /**
     * Whether the base phrase is a meta search.
     *
     * @var bool
     */
    public $is_meta;

    /**
     * Whether the term represents a range of values.
     *
     * @var bool
     */
    public $is_range;

    /**
     * The unprocessed term phrase.
     *
     * @var string
     */
    public $raw_phrase;

    /**
     * The processed phrase.
     *
     * @var string
     */
    public $phrase;

    /**
     * The ids of the resolved tags.
     *
     * @var array
     */
    public $tag_ids;

    /**
     * The namespace or meta phrase of the term.
     *
     * @var string
     */
    public $namespace;

    /**
     * The lower part of the phrase range.
     *
     * @var string
     */
    public $range_start;

    /**
     * The upper part of the phrase range.
     *
     * @var string
     */
    public $range_end;

    /**
     * Creates a SearchTerm from a string query's match array.
     *
     * @param array $term_match
     * @return SearchTerm
     */
    public static function parseStringTerm(array $term_match): SearchTerm 
    {
        $term = new SearchTerm();

        if(array_key_exists('is_negative', $term_match) && $term_match['is_negative'] != null)
        {
            $term->is_negative = true;
        }

        if(array_key_exists('is_fuzzy', $term_match) && $term_match['is_fuzzy'] != null)
        {
            $term->is_fuzzy = true;
        }

        if(array_key_exists('tag_name', $term_match) && $term_match['tag_name'] != null)
        {
            $term->raw_phrase = $term_match['tag_name'];
            $term->phrase = $term_match['tag_name'];
        }
        else if(array_key_exists('fuzzy_tag_name', $term_match) && $term_match['fuzzy_tag_name'] != null)
        {
            $term->raw_phrase = $term_match['fuzzy_tag_name'];
        }

        return $term;
    }
}