<?php

namespace App\Services\Search;

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
     *
     * @return SearchTerm
     */
    public static function parseStringTerm(array $term_match): SearchTerm
    {
        $term = new SearchTerm();

        if (array_key_exists('is_negative', $term_match) && $term_match['is_negative'] !== null) {
            $term->is_negative = true;
        }

        if (array_key_exists('is_fuzzy', $term_match) && $term_match['is_fuzzy'] !== null) {
            $term->is_fuzzy = true;
        }

        if (array_key_exists('tag_name', $term_match) && $term_match['tag_name'] !== null) {
            $term->raw_phrase = $term_match['tag_name'];
            $term->phrase = $term_match['tag_name'];
        } elseif (array_key_exists('fuzzy_tag_name', $term_match) && $term_match['fuzzy_tag_name'] !== null) {
            $term->raw_phrase = $term_match['fuzzy_tag_name'];
        }

        return $term;
    }
}
