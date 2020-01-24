<?php

namespace App\Services\Search;

class SearchTerm
{
    /**
     * Whether this term is a negative search term.
     *
     * @var bool
     */
    protected $is_negative;

    /**
     * Whether this term's phrase should be expanded.
     *
     * @var bool
     */
    protected $is_fuzzy;

    /**
     * Whether the base phrase is a tag.
     *
     * @var bool
     */
    protected $is_tag;

    /**
     * Whether the base phrase is a meta search.
     *
     * @var bool
     */
    protected $is_meta;

    /**
     * Whether the term represents a range of values.
     *
     * @var bool
     */
    protected $is_range;

    /**
     * The unprocessed term phrase.
     *
     * @var string
     */
    protected $raw_phrase;

    /**
     * The processed phrase.
     *
     * @var string
     */
    protected $phrase;

    /**
     * The ids of the resolved tags.
     *
     * @var array
     */
    protected $tag_ids;

    /**
     * The namespace or meta phrase of the term.
     *
     * @var string
     */
    protected $namespace;

    /**
     * The lower part of the phrase range.
     *
     * @var string
     */
    protected $range_start;

    /**
     * The upper part of the phrase range.
     *
     * @var string
     */
    protected $range_end;

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'tag_ids':
                $this->$name = $value;
                break;
        }
    }

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

        $term->is_negative = $term_match['is_negative'] !== null;

        $term->is_fuzzy = $term_match['is_fuzzy'] !== null;

        $term->raw_phrase = $term_match['tag_name'];
        $term->phrase = $term_match['tag_name'];

        $term->raw_phrase = $term_match['fuzzy_tag_name'];

        return $term;
    }
}
