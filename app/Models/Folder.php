<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UserAudits;

class Folder extends Model
{
    use SoftDeletes, UserAudits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cover_image',
        'folder_type_id',
        'access_type_id',
        'name', 
        'description',
    ];

    /**
     * Get this folder's folder type.
     */
    public function folder_type()
    {
        return $this->belongsTo('\App\Models\FolderType');
    }

    /**
     * Get this folder's access type.
     */
    public function access_type()
    {
        return $this->belongsTo('\App\Models\AccessType');
    }

    /**
     * Get public folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->whereHas('access_type', function($query){
            $query->public();
        });
    }

    /**
     * Get private folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivate($query)
    {
        return $query->whereHas('access_type', function($query){
            $query->private();
        });
    }

    /**
     * Get friend folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFriends($query)
    {
        return $query->whereHas('access_type', function($query){
            $query->friends();
        });
    }

    /**
     * Get unlisted folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnlisted($query)
    {
        return $query->whereHas('access_type', function($query){
            $query->unlisted();
        });
    }

    /**
     * Get favorites folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFavorites($query)
    {
        return $query->whereHas('folder_type', function($query){
            $query->favorites();
        });
    }

    /**
     * Get quick list folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQuickList($query)
    {
        return $query->whereHas('folder_type', function($query){
            $query->quickList();
        });
    }

    /**
     * Get book folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBooks($query)
    {
        return $query->whereHas('folder_type', function($query){
            $query->book();
        });
    }

    /**
     * Get generic folders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGeneric($query)
    {
        return $query->whereHas('folder_type', function($query){
            $query->generic();
        });
    }
}
