<?php

namespace App\Services\Search;

use Illuminate\Support\Collection;

class SearchGroup
{
    public const MODE_AND = 'and';

    public const MODE_OR = 'or';

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
    public $conjunction = SearchGroup::MODE_AND;

    /**
     * Returns a flat array of all terms in this group, recursively.
     *
     * @return array
     */
    public function allTerms(): array
    {
        $terms = $this->terms ?? [];

        if ($this->groups) {
            foreach ($this->groups as $group) {
                $terms = array_merge($terms, $group->allTerms());
            }
        }

        return $terms;
    }

    /**
     * Assigns ids to the tags terms in the group and its child groups.
     *
     * @param Collection $tag_ids
     *
     * @return void
     */
    public function assignTagIds(Collection $tag_ids)
    {
        if ($this->terms) {
            foreach ($this->terms as $term) {
                $term->tag_ids[] = $tag_ids->firstWhere('name', $term->phrase)->id;
            }
        }

        if ($this->groups) {
            foreach ($this->groups as $group) {
                $group->assignTagIds($tag_ids);
            }
        }
    }
}
