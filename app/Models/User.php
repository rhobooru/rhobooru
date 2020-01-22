<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use App\Scopes\RealUserScope;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * The attributes that should be cast to different types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'system_account' => 'bool',
        'anonymous_account' => 'bool',
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

        static::addGlobalScope(new RealUserScope);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
    
    /**
     * Get the users's preferred date format.
     */
    // public function date_format(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo('App\Models\DateFormat');
    // }

    /**
     * Get the users's preferred site theme.
     */
    // public function site_theme(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo('App\Models\SiteTheme');
    // }

    /**
     * Get the users's preferred record fit.
     */
    // public function record_fit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo('App\Models\RecordFit');
    // }

    /**
     * Get the users's preferred max content rating.
     */
    // public function max_content_rating(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo('App\Models\ContentRating', 'maximum_content_rating_id');
    // }

    /**
     * Get the user's folders.
     */
    public function folders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Folder', 'created_by_user_id');
    }

    /**
     * Get the user's records.
     */
    public function records(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Record', 'created_by_user_id');
    }

    /**
     * Get the user's favorites folder.
     */
    public function favoritesFolder(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\Folder', 'created_by_user_id')->favorites();
    }

    /**
     * Get the user's quick list folder.
     */
    public function quickListFolder(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\Folder', 'created_by_user_id')->quickList();
    }

    /**
     * Get the user's generic folders.
     */
    public function genericFolders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->folders()->generic();
    }

    /**
     * Get the user's book folders.
     */
    public function bookFolders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->folders()->books();
    }

    /**
     * Restrict a query to the anonymous user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsAnonymous($query)
    {
        return $query->where('anonymous_account', true);
    }

    /**
     * Restrict a query to users that aren't the anonymous user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsNotAnonymous($query)
    {
        return $query->where(function ($q) {
            $q->where('anonymous_account', false)
                  ->orWhereNull('anonymous_account');
        });
    }

    /**
     * Restrict a query to system accounts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystemAccount($query)
    {
        return $query->where('system_account', true);
    }









    /**
     * Creates all the related models for a user (eg. settings and folders).
     */
    public function createUserRelationships()
    {
        // Create user's default settings.
        $this->createUserSettings();

        // Create user's favorites folder.
        $this->createUserFavoritesFolder();

        // Create user's quick list folder.
        $this->createUserQuickListFolder();
    }

    /**
     * Creates the user's default settings.
     *
     * @return void
     */
    public function createUserSettings()
    {
        // TODO
    }

    /**
     * Creates the user's favorites folder, if it doesn't exist.
     * 
     * @return  \App\Models\Folder
     */
    public function createUserFavoritesFolder()
    {
        if($this->favoritesFolder()->exists())
            return $this->favoritesFolder->first();

        return \App\Models\Folder::create([
            'created_by_user_id' => $this->id,
            'updated_by_user_id' => $this->id,
            'folder_type_id' => \App\Models\FolderType::favorites()->first()->id,
            'access_type_id' => \App\Models\AccessType::public()->first()->id,
            'name' => 'Favorites',
        ]);
    }

    /**
     * Creates the user's quick list folder, if it doesn't exist.
     * 
     * @return  \App\Models\Folder
     */
    public function createUserQuickListFolder()
    {
        if($this->quickListFolder()->exists())
            return $this->quickListFolder->first();

        return \App\Models\Folder::create([
            'created_by_user_id' => $this->id,
            'updated_by_user_id' => $this->id,
            'folder_type_id' => \App\Models\FolderType::quickList()->first()->id,
            'access_type_id' => \App\Models\AccessType::private()->first()->id,
            'name' => 'Quick List',
        ]);
    }

    /**
     * Soft deletes the soft-deleteable related models for a user (eg. folders).
     */
    public function softDeleteUserRelationships()
    {
        // Delete the user's special folders.
        $this->favoritesFolder()->delete();
        $this->quickListFolder()->delete();

        // Delete the user's restricted-access folders.
        $this->genericFolders()->private()->delete();
        $this->bookFolders()->private()->delete();
        $this->genericFolders()->friends()->delete();
        $this->bookFolders()->friends()->delete();

        // Leave the user's public and unlisted folders since those
        // might still be used by others.
    }

    /**
     * Force deletes all the related models for a user (eg. settings and folders).
     */
    public function forceDeleteUserRelationships()
    {
        // Delete the user's settings.
        // TODO

        // Delete the user's folders.
        $this->folders()->forceDelete();
    }

    /**
     * Restores the soft-deleteable related models for a user (eg. folders).
     */
    public function restoreUserRelationships()
    {
        // Restore the user's special folders.
        $this->favoritesFolder()->restore();
        $this->quickListFolder()->restore();

        // Restore the user's restricted-access folders.
        $this->genericFolders()->private()->restore();
        $this->bookFolders()->private()->restore();
        $this->genericFolders()->friends()->restore();
        $this->bookFolders()->friends()->restore();

        // Leave the user's public and unlisted folders since those
        // aren't soft-deleted by the user's deleted status.
    }

    /**
     * Returns the anonymous user account.
     *
     * @return App\Models\User
     */
    public static function anonymous(): User
    {
        return User::withoutGlobalScopes()
            ->findOrFail(config('rhobooru.users.anonymous_user_id'));
    }

    /**
     * Get all user's permissions.
     *
     * @return Object|null
     */
    public function getAllPermissionsAttribute(): ?Object
    {
        return $this->getAllPermissions();
    }

    /**
     * Get a url to the user's avatar.
     *
     * @return string|null
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if(!$this->avatar){
            return null;
        }

        return asset('storage/avatars/' . $this->avatar);
    }
}
