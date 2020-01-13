<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessType extends Model
{
    /**
     * These constants are the valid values for 
     * the `access_types`.`static_name` field.
     * 
     * They should be used for business logic purposes.
     */
    const PUBLIC_ACCESS     = 'public';
    const UNLISTED_ACCESS   = 'unlisted';
    const FRIENDS_ACCESS    = 'friends';
    const PRIVATE_ACCESS    = 'private';
    
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
        'name', 
        'description',
    ];

    /**
     * Get the folders that belong to this access type.
     */
    public function folders(){
        return $this->hasMany('App\Models\Folder');
    }

    /**
     * Get the public access type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic($query)
    {
        return $query->where('static_name', self::PUBLIC_ACCESS);
    }

    /**
     * Get the unlisted access type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnlisted($query)
    {
        return $query->where('static_name', self::UNLISTED_ACCESS);
    }

    /**
     * Get the friends access type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFriends($query)
    {
        return $query->where('static_name', self::FRIENDS_ACCESS);
    }

    /**
     * Get the private access type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivate($query)
    {
        return $query->where('static_name', self::PRIVATE_ACCESS);
    }
}
