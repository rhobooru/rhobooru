<?php

namespace App\Services\Search;

use Illuminate\Support\Collection;

class SearchGroup
{
    public const AND = 'and';

    public const OR = 'or';

    /**
     * The SearchTerms of this groups.
     *
     * @var array
     */
    protected $terms;

    /**
     * Nested SearchGroups within this group.
     *
     * @var array
     */
    protected $groups;

    /**
     * Whether this is an AND or an OR group.
     *
     * @var string
     */
    protected $conjunction = SearchGroup::AND;

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'conjunction':
                $this->$name = $value;
                break;
        }
    }

    /**
     * Returns a flat array of all terms in this group, recursively.
     *
     * @return array
     */
    public function allTerms(): array
    {
        $terms = $this->terms ?? [];

        foreach ($this->groups as $group) {
            $terms = array_merge($terms, $group->allTerms());
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
        foreach ($this->terms as $term) {
            $term->tag_ids[] = $tag_ids->firstWhere('name', $term->phrase)->id;
        }

        foreach ($this->groups as $group) {
            $group->assignTagIds($tag_ids);
        }
    }
}
