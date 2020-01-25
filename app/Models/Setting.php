<?php

namespace App\Models;

use App\Scopes\SortedScope;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Setting extends Eloquent implements Sortable
{
    use SortableTrait;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Sortable confg.
     *
     * @var array
     */
    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        // @codeCoverageIgnoreStart
        parent::boot();

        static::addGlobalScope(new SortedScope);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get the group to which this group belongs.
     */
    public function setting_group()
    {
        return $this->belongsTo('App\Models\SettingGroup');
    }

    /**
     * Scope for the sortable code.
     *
     * @return void
     */
    public function buildSortQuery()
    {
        // Retrict sort logic to just this item's siblings.
        return static::query()
            ->where('system_setting', $this->system_setting === 1)
            ->where('setting_group_id', $this->setting_group_id);
    }

    /**
     * Get the settings config key.
     *
     * @return string
     */
    public function getConfigKeyAttribute(): string
    {
        return "{$this->setting_group->key}.{$this->key}";
    }

    /**
     * Checks that the new value is valid for this setting's
     * options.
     *
     * @param mixed $new_value
     *
     * @return void
     */
    public function validate($new_value)
    {
        if ($new_value === null) {
            return $this->allow_null;
        }

        if (is_numeric($new_value)) {
            if ($this->minimum_value && $this->minimum_value > $new_value) {
                return false;
            }

            if ($this->maximum_value && $this->maximum_value < $new_value) {
                return false;
            }
        }

        return true;
    }
}
