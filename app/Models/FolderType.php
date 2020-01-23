<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderType extends Model
{
    /**
     * These constants are the valid values for
     * the `folder_types`.`static_name` field.
     *
     * They should be used for business logic purposes.
     */
    public const FAVORITES_FOLDER_TYPE = 'favorites';
    public const QUICK_LIST_FOLDER_TYPE = 'quick_list';
    public const BOOK_FOLDER_TYPE = 'book';
    public const GENERIC_FOLDER_TYPE = 'generic';

    /**
     * Whether to allow created_at and updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'static_name',
        'can_be_managed_manually',
        'name',
        'description',
    ];

    /**
     * Get the folders that belong to this folder type.
     */
    public function folders()
    {
        return $this->hasMany('App\Models\Folder');
    }

    /**
     * Get the favorites folder type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFavorites($query)
    {
        return $query->where('static_name', self::FAVORITES_FOLDER_TYPE);
    }

    /**
     * Get the quick list folder type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQuickList($query)
    {
        return $query->where('static_name', self::QUICK_LIST_FOLDER_TYPE);
    }

    /**
     * Get the book folder type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBook($query)
    {
        return $query->where('static_name', self::BOOK_FOLDER_TYPE);
    }

    /**
     * Get the generic folder type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGeneric($query)
    {
        return $query->where('static_name', self::GENERIC_FOLDER_TYPE);
    }

    /**
     * Get the folder types that are managed manually.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeManagedManually($query)
    {
        return $query->where('can_be_managed_manually', true);
    }
}
